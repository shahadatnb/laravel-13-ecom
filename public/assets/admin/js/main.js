/* ──────────────────────────────────────────────
   Unsaved Changes Warning System
   ────────────────────────────────────────────── */
/**
 * Warns users before leaving a page with unsaved form changes.
 * Handles browser close/refresh (beforeunload) and internal link clicks.
 *
 * Usage:
 *   UnsavedChanges.watch(document.getElementById('my-form'));
 *   UnsavedChanges.ignore('/some-safe-href'); // skip warning for specific links
 *   UnsavedChanges.markClean(); // reset dirty flag (call on successful save)
 *   UnsavedChanges.isDirty(); // check if form has changes
 */
const UnsavedChanges = {
    _dirty: false,
    _message: 'You have unsaved changes. Are you sure you want to leave?',
    _form: null,
    _boundCheck: null,
    _boundLink: null,
    _ignoreUrls: [],
    _badge: null,

    /**
     * Start watching a form for changes.
     * @param {HTMLFormElement} form
     * @param {string} [message] Custom confirmation message
     */
    watch: function(form, message) {
        if (!form) return;
        this._form = form;
        if (message) this._message = message;

        // Snapshot initial field values
        this._saveInitialState();

        // Listen for changes on all input, select, textarea
        var self = this;
        this._boundCheck = function() { self._markDirty(); };

        var fields = form.querySelectorAll('input, select, textarea');
        fields.forEach(function(field) {
            // Change event covers: select, checkbox, radio
            field.addEventListener('change', self._boundCheck);
            // Input event covers: text, number, file, textarea
            field.addEventListener('input', self._boundCheck);
        });

        // beforeunload — browser close / refresh / back
        window.addEventListener('beforeunload', function(e) {
            if (!self._dirty) return;
            e.preventDefault();
            e.returnValue = self._message;
            return self._message;
        });

        // Intercept link clicks for internal navigation
        this._boundLink = function(e) {
            if (!self._dirty) return;
            var link = e.currentTarget;
            var href = link.getAttribute('href') || '';

            // Skip safe/ignore urls
            for (var i = 0; i < self._ignoreUrls.length; i++) {
                if (href.indexOf(self._ignoreUrls[i]) !== -1) return;
            }

            // Skip external links, hash-anchors, javascript:, mailto:, tel:
            if (href.indexOf('://') !== -1
                || href.indexOf('javascript:') === 0
                || href.indexOf('mailto:') === 0
                || href.indexOf('tel:') === 0
                || href === '#'
                || href.indexOf('#') === 0) return;

            // Skip links that open in new tab
            if (link.getAttribute('target') === '_blank') return;

            // Show confirmation
            if (!confirm(self._message)) {
                e.preventDefault();
                e.stopPropagation();
            }
        };

        // Attach link interceptor to all links on the page
        document.querySelectorAll('a').forEach(function(a) {
            a.addEventListener('click', self._boundLink);
        });

        // Also intercept form submissions that might redirect
        form.addEventListener('submit', function() {
            // Don't mark dirty reset here — the AJAX handler or page redirect will
            // call markClean() on success
        });

        // Show dirty badge on the first submit button's container
        this._createBadge();
    },

    /**
     * Add a URL pattern to ignore (no warning).
     * @param {string} urlPattern
     */
    ignore: function(urlPattern) {
        this._ignoreUrls.push(urlPattern);
    },

    /**
     * Mark the form as clean (no unsaved changes).
     * Call this after a successful AJAX save.
     */
    markClean: function() {
        this._dirty = false;
        this._updateBadge();
    },

    /**
     * Check if the form has unsaved changes.
     */
    isDirty: function() {
        return this._dirty;
    },

    /**
     * Manual dirty override (for dynamically added fields).
     */
    markDirty: function() {
        this._markDirty();
    },

    /**
     * Refresh the initial snapshot (after fields are dynamically populated).
     */
    refreshSnapshot: function() {
        this._saveInitialState();
        this._dirty = false;
        this._updateBadge();
    },

    // ─── Internal ───

    _saveInitialState: function() {
        if (!this._form) return;
        this._initialState = {};
        var fields = this._form.querySelectorAll('input, select, textarea');
        fields.forEach(function(field) {
            var name = field.getAttribute('name');
            if (!name) return;
            if (field.type === 'file') {
                // File inputs: track as empty
                this._initialState[name] = '';
            } else if (field.type === 'checkbox') {
                this._initialState[name + '_' + field.value] = field.checked;
            } else if (field.type === 'radio') {
                if (field.checked) {
                    this._initialState[name] = field.value;
                }
            } else {
                this._initialState[name] = field.value;
            }
        }, this);
    },

    _markDirty: function() {
        if (this._dirty) return;
        // Quick check: compare current state to initial state
        if (!this._form) return;

        var fields = this._form.querySelectorAll('input, select, textarea');
        var changed = false;

        for (var i = 0; i < fields.length; i++) {
            var field = fields[i];
            var name = field.getAttribute('name');
            if (!name) continue;

            var initial;
            if (field.type === 'checkbox') {
                initial = this._initialState[name + '_' + field.value];
                if (initial !== field.checked) { changed = true; break; }
            } else if (field.type === 'radio') {
                initial = this._initialState[name];
                if (field.checked && field.value !== initial) { changed = true; break; }
            } else if (field.type === 'file') {
                if (field.files && field.files.length > 0) { changed = true; break; }
            } else {
                initial = this._initialState[name];
                if (field.value !== initial) { changed = true; break; }
            }
        }

        if (changed) {
            this._dirty = true;
            this._updateBadge();
        }
    },

    _createBadge: function() {
        if (this._badge) return;
        this._badge = document.createElement('div');
        this._badge.className = 'unsaved-badge';
        this._badge.title = 'Unsaved changes';
        this._badge.style.display = 'none';
        this._badge.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Unsaved changes — don\'t forget to save! <button type="button" class="unsaved-dismiss" onclick="this.parentElement.style.display=\'none\'">&times;</button>';

        // Insert it at the top of the form so it's immediately visible
        if (this._form) {
            this._form.insertBefore(this._badge, this._form.firstChild);
        }
    },

    _updateBadge: function() {
        if (!this._badge) return;
        this._badge.style.display = this._dirty ? 'block' : 'none';
    }
};

/* ──────────────────────────────────────────────
   Toast Notification System
   ────────────────────────────────────────────── */
const Toast = {
    /**
     * Show a toast notification.
     *
     * @param {string} message  Main message text
     * @param {string} type     'success' | 'error' | 'warning' | 'info'
     * @param {object} options  { title, duration (ms), iconClass }
     * @returns {HTMLElement} The toast element
     */
    show: function(message, type, options) {
        type = type || 'info';
        options = options || {};
        var title = options.title || '';
        var duration = options.duration || (type === 'success' ? 4000 : type === 'error' ? 6000 : 5000);

        // Ensure container exists
        var container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }

        // Icon mapping
        var icons = {
            success: 'fa-check',
            error: 'fa-times',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info'
        };
        var iconClass = options.iconClass || icons[type] || 'fa-bell';

        // Title defaults
        var titles = {
            success: 'Success!',
            error: 'Error!',
            warning: 'Warning!',
            info: 'Notice'
        };
        if (!title) title = titles[type] || '';

        // Build toast (use kd- prefix to avoid colliding with Bootstrap's .toast class)
        var toast = document.createElement('div');
        toast.className = 'kd-toast kd-toast-' + type;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = ''
            + '<div class="kd-toast-icon"><i class="fas ' + iconClass + '"></i></div>'
            + '<div class="kd-toast-body">'
            + (title ? '<div class="kd-toast-title">' + this._escape(title) + '</div>' : '')
            + '<div class="kd-toast-message">' + this._escape(message) + '</div>'
            + '</div>'
            + '<button type="button" class="kd-toast-close" aria-label="Close">&times;</button>'
            + '<div class="kd-toast-progress"></div>';

        container.appendChild(toast);

        // Close button
        var closeBtn = toast.querySelector('.kd-toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                Toast.dismiss(toast);
            });
        }

        // Set progress bar duration
        var progress = toast.querySelector('.kd-toast-progress');
        if (progress) {
            progress.style.animationDuration = duration + 'ms';
        }

        // Auto-dismiss
        if (duration > 0) {
            toast._hideTimer = setTimeout(function() {
                Toast.dismiss(toast);
            }, duration);
        }

        return toast;
    },

    /**
     * Dismiss a toast with animation.
     */
    dismiss: function(toast) {
        if (!toast || toast._dismissing) return;
        toast._dismissing = true;

        // Clear auto-hide timer
        if (toast._hideTimer) {
            clearTimeout(toast._hideTimer);
            toast._hideTimer = null;
        }

        toast.classList.add('kd-toast-leaving');

        // Remove from DOM after animation
        setTimeout(function() {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    },

    /**
     * Success shorthand.
     */
    success: function(message, title, duration) {
        return this.show(message, 'success', { title: title, duration: duration });
    },

    /**
     * Error shorthand.
     */
    error: function(message, title, duration) {
        return this.show(message, 'error', { title: title, duration: duration });
    },

    /**
     * Warning shorthand.
     */
    warning: function(message, title, duration) {
        return this.show(message, 'warning', { title: title, duration: duration });
    },

    /**
     * Info shorthand.
     */
    info: function(message, title, duration) {
        return this.show(message, 'info', { title: title, duration: duration });
    },

    /**
     * Simple HTML escape utility.
     */
    _escape: function(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }
};

/**
 * Convert Laravel flash messages from the server into toasts.
 * Shows a toast AND auto-dismisses the visible alert after a short delay.
 * The visible alert remains as a non-JS fallback.
 */
function convertFlashToasts() {
    var types = [
        { selector: '[data-flash-toast="success"]',   method: 'success' },
        { selector: '[data-flash-toast="error"]',     method: 'error'   },
        { selector: '[data-flash-toast="warning"]',   method: 'warning' },
        { selector: '[data-flash-toast="info"]',      method: 'info'    }
    ];

    types.forEach(function(t) {
        var flash = document.querySelector(t.selector);
        if (flash) {
            Toast[t.method](flash.textContent.trim());
            setTimeout(function() {
                if (flash.parentNode) {
                    flash.style.transition = 'opacity 0.5s ease';
                    flash.style.opacity = '0';
                    setTimeout(function() {
                        if (flash.parentNode) flash.remove();
                    }, 500);
                }
            }, 1000);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Convert any server-side flash messages to toasts on page load
    convertFlashToasts();
});

function printElement($element, options = {}) {
    const defaults = {
        company: 'প্রতিষ্ঠানের নাম',
        address: 'প্রতিষ্ঠানের ঠিকানা',
        title: 'প্রিন্ট ডকুমেন্ট',
        orientation: 'portrait', // landscape
        header: true,
        footer: false,
        printTime: true
    };
    
    const settings = {...defaults, ...options};
    const printContent = $element.clone();
    
    // প্রিন্ট উইন্ডো তৈরি
    const printWindow = window.open('', '_blank');
    
    // হেডার যোগ
    if (settings.header) {
        printContent.prepend(`
            <div class="header">
                <h3 class="company">${settings.company}</h3>
                <p class="address">${settings.address}</p>
                <h2 class="report-title">${settings.title}</h2>`);
                if(settings.sub_title){
                    printContent.append(`
                        <p class="sub_title">${settings.sub_title}</p>
                    `);
                }
        if (settings.printTime) {
            printContent.append(`
                <p class="print-date">Print Date: ${new Date().toLocaleString()}</p>
            `);
        }
        printContent.append(`
            </div>
        `);
    }
    //প্রিন্ট তারিখ: ${new Date().toLocaleDateString('bn-BD')}</p>
    // ফুটার যোগ
    if (settings.footer) {
        let footerContent = `
            <div class="footer" style="text-align: center; margin-top: 5px; position: relative;">
                <div style="border-top: 1px solid #000; padding-top: 5px;">
                    <p style="margin-bottom: 5px;">Print Date: ${new Date().toLocaleString()}</p>
                </div>
            </div>`;
        printContent.append(footerContent);
    }
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>${settings.title}</title>
            <style>
                body { font-family: Arial; margin: 10px; font-size: 12px; }
                .header { text-align: center; 
                    border-bottom: 2px solid #252525ff; 
                    padding-bottom: 5px; 
                    position: relative;
                    margin-bottom: 7px; 
                }
                h1, h2, h3 { margin: 0; }
                p { margin: 0; }
                .company { font-size: 20px; font-weight: bold; }
                .address { font-size: 14px; margin-top: 5px; }
                .report-title { font-size: 18px; font-weight: bold; margin-top: 5px; }
                .print-date {
                    position: absolute;
                    right: 5px;
                    bottom: 5px;
                    text-align: right;
                    font-size: 12px; margin-top: 5px; 
                 }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .text-bold { font-weight: bold; }
                .text-underline { text-decoration: underline; }
                .text-italic { font-style: italic; }
                .text-uppercase { text-transform: uppercase; }
                .text-lowercase { text-transform: lowercase; }
                .text-capitalize { text-transform: capitalize; }
                table { width: 100%; border-collapse: collapse; }
                th, td { 
                    border: 1px solid #000; 
                    padding: 2px; 
                }
                .d-print-none { display: none; }
                @page {
                    size: ${settings.orientation === 'landscape' ? 'A4 landscape' : 'A4 portrait'};
                    @bottom-center {
                        content: "Page " counter(page) " of " counter(pages);
                        font-family: Arial;
                        font-size: 10px;
                    }
                }
            </style>
        </head>
        <body>
            ${printContent.html()}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    // স্বয়ংক্রিয় প্রিন্ট
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}