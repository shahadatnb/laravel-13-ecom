/**
 * VariantImageManager — reusable module for variant image upload & preview
 *
 * Dependencies: none (vanilla JS)
 *
 * Usage:
 *   // Create upload UI for a new variant
 *   var mgr = new VariantImageManager('new-0');
 *   var cell = mgr.render();            // returns <td> element
 *   myRow.appendChild(cell);
 *
 *   // Create upload UI for an existing variant with pre-loaded images
 *   var mgr2 = new VariantImageManager(5, existingImagesArray);
 *   var cell2 = mgr2.render();
 *   myRow.appendChild(cell2);
 *
 *   // Get all files for form submission
 *   var allFiles = VariantImageManager.collectFiles(); // { index: File[] }
 */
(function (window) {
  'use strict';

  /**
   * @param {string|number} variantKey  Unique key for the variant (Variant ID for existing, index for new)
   * @param {Array}         [existingImages=[]]  Array of { id, image } for pre-loaded images
   */
  function VariantImageManager(variantKey, existingImages, storageBaseUrl) {
    this.key = variantKey;
    this.existingImages = existingImages || [];
    // Normalize storage base URL: ensure trailing slash
    this.storageBaseUrl = (storageBaseUrl || '/storage/').replace(/\/?$/, '/');
    this.newFiles = [];          // File objects uploaded for this variant
    this._containerEl = null;    // The preview container DOM element
    this._inputEl = null;        // The hidden file input
  }

  /** Max file size in bytes (2 MB) */
  VariantImageManager.prototype.MAX_FILE_SIZE = 2 * 1024 * 1024;

  /** Accepted MIME types */
  VariantImageManager.prototype.ACCEPTED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

  /** Max number of images per variant */
  VariantImageManager.prototype.MAX_IMAGES = 5;

  /** Placeholder data-URI SVG shown when an existing image URL fails to load */
  VariantImageManager.prototype.PLACEHOLDER_SVG = 'data:image/svg+xml,' + encodeURIComponent(
    '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50">'
    + '<rect width="50" height="50" fill="#f3f4f6" rx="4"/>'
    + '<path d="M17 21l16 0 0 12-16 0z" fill="#d1d5db"/>'
    + '<circle cx="19" cy="22" r="2" fill="#9ca3af"/>'
    + '<path d="M17 33l6-8 4 5 3-2 3 5z" fill="#9ca3af"/>'
    + '<text x="25" y="44" text-anchor="middle" font-size="7" fill="#9ca3af">No Image</text>'
    + '</svg>'
  );

  // ──────────────────────────────────────────────
  //  Public: render the <td> element
  // ──────────────────────────────────────────────
  VariantImageManager.prototype.render = function () {
    var self = this;
    var td = document.createElement('td');
    td.className = 'variant-image-cell';

    var container = document.createElement('div');
    container.className = 'variant-image-upload-container';
    td.appendChild(container);

    // Preview area
    var preview = document.createElement('div');
    preview.className = 'variant-image-preview';
    preview.setAttribute('data-variant-key', this.key);
    container.appendChild(preview);
    this._containerEl = preview;

    // Hidden file input
    var input = document.createElement('input');
    input.type = 'file';
    input.name = 'variant_images[' + this.key + '][]';
    input.className = 'variant-image-input d-none';
    input.accept = 'image/*';
    input.multiple = true;
    container.appendChild(input);
    this._inputEl = input;

    // Upload button
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'btn btn-sm btn-primary variant-upload-btn';
    btn.innerHTML = '<i class="fas fa-upload"></i> Upload';
    container.appendChild(btn);

    // Hint text
    var hint = document.createElement('small');
    hint.className = 'd-block text-muted mt-1 upload-hint';
    hint.textContent = 'Upload variant images';
    container.appendChild(hint);

    // ─── Event: button click → file picker ───
    btn.addEventListener('click', function () {
      input.click();
    });

    // ─── Event: file selected ───
    input.addEventListener('change', function (e) {
      self._handleFiles(e.target.files);
      // Reset so re-selecting the same file(s) triggers change
      this.value = '';
    });

    // ─── Render existing images (if any) ───
    if (this.existingImages.length > 0) {
      this.existingImages.forEach(function (img) {
        self._renderExistingImage(img);
      });
    }

    return td;
  };

  // ──────────────────────────────────────────────
  //  Public: collect all files from ALL instances
  //  Returns: { [key: string]: File[] }
  // ──────────────────────────────────────────────
  VariantImageManager.collectFiles = function () {
    var all = {};
    if (window.__variantManagers) {
      Object.keys(window.__variantManagers).forEach(function (key) {
        var mgr = window.__variantManagers[key];
        if (mgr.newFiles.length > 0) {
          all[key] = mgr.newFiles.slice(); // copy
        }
      });
    }
    return all;
  };

  // ──────────────────────────────────────────────
  //  Public: get removed existing image IDs
  //  Uses disabled hidden inputs to track removals (standard form submission)
  //  Returns: number[]
  // ──────────────────────────────────────────────
  VariantImageManager.getRemovedIds = function () {
    // Disabled hidden inputs within removed wrappers are not submitted.
    // The server receives fewer IDs than expected and treats missing IDs as deleted.
    // For AJAX use, call this to collect all removed IDs.
    var ids = [];
    if (window.__variantManagers) {
      Object.keys(window.__variantManagers).forEach(function (key) {
        var mgr = window.__variantManagers[key];
        ids = ids.concat(mgr._removedExistingIds || []);
      });
    }
    return ids;
  };

  // ──────────────────────────────────────────────
  //  Internal: validate & process selected files
  // ──────────────────────────────────────────────
  VariantImageManager.prototype._handleFiles = function (fileList) {
    var self = this;
    var validFiles = [];

    Array.from(fileList).forEach(function (file) {
      // Type check
      if (self.ACCEPTED_TYPES.indexOf(file.type) === -1) {
        alert(file.name + ' is not a supported image. Accepted: JPG, PNG, WEBP, GIF');
        return;
      }
      // Size check
      if (file.size > self.MAX_FILE_SIZE) {
        alert(file.name + ' exceeds 2 MB size limit.');
        return;
      }
      validFiles.push(file);
    });

    // Count check
    var totalAfter = self.newFiles.length + validFiles.length;
    if (totalAfter > self.MAX_IMAGES) {
      alert('Maximum ' + self.MAX_IMAGES + ' images per variant. Current: ' + self.newFiles.length + ', adding: ' + validFiles.length);
      return;
    }

    validFiles.forEach(function (file) {
      self.newFiles.push(file);
      self._previewNewFile(file);
    });
  };

  // ──────────────────────────────────────────────
  //  Internal: render a single new file preview
  // ──────────────────────────────────────────────
  VariantImageManager.prototype._previewNewFile = function (file) {
    var self = this;
    var reader = new FileReader();

    reader.onload = function (e) {
      var wrapper = document.createElement('div');
      wrapper.className = 'variant-preview-img-wrapper';

      var img = document.createElement('img');
      img.src = e.target.result;
      img.className = 'variant-preview-img';
      img.alt = file.name;

      var removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'remove-variant-img';
      removeBtn.innerHTML = '<i class="fas fa-times"></i>';
      removeBtn.title = 'Remove image';
      removeBtn.addEventListener('click', function () {
        // Remove from the newFiles array
        var idx = self.newFiles.indexOf(file);
        if (idx !== -1) self.newFiles.splice(idx, 1);
        wrapper.remove();
        self._toggleHasImages();
      });

      wrapper.appendChild(img);
      wrapper.appendChild(removeBtn);
      self._containerEl.appendChild(wrapper);
      self._toggleHasImages();
    };

    reader.readAsDataURL(file);
  };

  // ──────────────────────────────────────────────
  //  Internal: render a single existing image
  // ──────────────────────────────────────────────
  VariantImageManager.prototype._renderExistingImage = function (imgData) {
    var self = this;
    var wrapper = document.createElement('div');
    wrapper.className = 'variant-preview-img-wrapper';
    wrapper.setAttribute('data-existing-img-id', imgData.id);

    var img = document.createElement('img');
    // Strip leading slash from image path to prevent double-slash with storageBaseUrl
    var cleanPath = imgData.image ? imgData.image.replace(/^\/+/, '') : '';
    img.src = imgData.url || (this.storageBaseUrl + cleanPath);
    img.className = 'variant-preview-img';
    img.alt = imgData.image || 'Variant image';
    // Fallback to placeholder on load error (broken URL, missing file, etc.)
    img.addEventListener('error', function () {
      img.src = self.PLACEHOLDER_SVG;
      img.style.objectFit = 'contain';
    });

    // Hidden input to keep track of this existing image
    var hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'variant_existing_images[' + this.key + '][]';
    hiddenInput.value = imgData.id;
    wrapper.appendChild(hiddenInput);

    var removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'remove-variant-img';
    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
    removeBtn.title = 'Remove image';
    removeBtn.addEventListener('click', function () {
      if (!self._removedExistingIds) self._removedExistingIds = [];
      self._removedExistingIds.push(imgData.id);
      hiddenInput.disabled = true;   // don't submit
      wrapper.remove();
      self._toggleHasImages();
    });

    wrapper.appendChild(img);
    wrapper.appendChild(removeBtn);
    this._containerEl.appendChild(wrapper);
    this._toggleHasImages();
  };

  // ──────────────────────────────────────────────
  //  Internal: toggle the "has-images" visual state
  // ──────────────────────────────────────────────
  VariantImageManager.prototype._toggleHasImages = function () {
    if (!this._containerEl) return;
    if (this._containerEl.children.length > 0) {
      this._containerEl.classList.add('has-images');
    } else {
      this._containerEl.classList.remove('has-images');
    }
  };

  // ──────────────────────────────────────────────
  //  Register globally & auto-track instances
  // ──────────────────────────────────────────────
  window.VariantImageManager = VariantImageManager;

  // Storage for all instances (for collectFiles / getRemovedIds)
  if (!window.__variantManagers) {
    window.__variantManagers = {};
  }

  // Patch: after creation, register the instance
  var origRender = VariantImageManager.prototype.render;
  VariantImageManager.prototype.render = function () {
    var td = origRender.call(this);
    window.__variantManagers[this.key] = this;
    return td;
  };

})(window);
