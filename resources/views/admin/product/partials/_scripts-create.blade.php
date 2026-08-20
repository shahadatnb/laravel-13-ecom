<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== THUMBNAIL UPLOAD (Create - Preview Only) ==========
    const thumbnailInput = document.getElementById('thumbnail');
    const thumbnailUploadBtn = document.getElementById('thumbnail-upload-btn');
    const thumbnailPreviewBox = document.getElementById('thumbnail-preview-box');

    if (thumbnailUploadBtn) {
        thumbnailUploadBtn.addEventListener('click', function() {
            thumbnailInput.click();
        });
    }

    if (thumbnailInput) {
        thumbnailInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size should not exceed 5MB.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const placeholder = thumbnailPreviewBox.querySelector('.thumbnail-placeholder');
                    if (placeholder) placeholder.remove();

                    const img = document.createElement('img');
                    img.id = 'thumbnail-preview';
                    img.src = e.target.result;
                    img.alt = 'Thumbnail Preview';
                    thumbnailPreviewBox.insertBefore(img, thumbnailPreviewBox.firstChild);

                    const overlay = document.createElement('div');
                    overlay.className = 'thumbnail-overlay';
                    overlay.id = 'thumbnail-overlay';
                    overlay.innerHTML = '<button type="button" class="btn-remove-thumbnail" id="thumbnail-remove-btn" title="Remove"><i class="fas fa-trash"></i></button>';
                    thumbnailPreviewBox.appendChild(overlay);

                    overlay.querySelector('#thumbnail-remove-btn').addEventListener('click', removeThumbnail);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function removeThumbnail() {
        if (confirm('Are you sure you want to remove the thumbnail?')) {
            thumbnailInput.value = '';
            const img = thumbnailPreviewBox.querySelector('img');
            if (img) img.remove();
            const overlay = thumbnailPreviewBox.querySelector('.thumbnail-overlay');
            if (overlay) overlay.remove();
            const placeholder = document.createElement('div');
            placeholder.className = 'thumbnail-placeholder';
            placeholder.innerHTML = '<i class="fas fa-image fa-3x text-muted"></i><p class="mt-2 mb-0 text-muted">No thumbnail selected</p>';
            thumbnailPreviewBox.appendChild(placeholder);
        }
    }

    // ========== GALLERY DROP ZONE (Create - Preview Only) ==========
    const galleryDropZone = document.getElementById('gallery-drop-zone');
    const galleryInput = document.getElementById('gallery-input');
    const newGalleryPreview = document.getElementById('new-gallery-preview');
    const maxGalleryImages = 10;
    let newGalleryFiles = [];

    if (galleryDropZone) {
        galleryDropZone.addEventListener('click', function() {
            galleryInput.click();
        });

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            galleryDropZone.addEventListener(eventName, (e) => { e.preventDefault(); e.stopPropagation(); }, false);
            document.body.addEventListener(eventName, (e) => { e.preventDefault(); e.stopPropagation(); }, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            galleryDropZone.addEventListener(eventName, () => galleryDropZone.classList.add('drag-over'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            galleryDropZone.addEventListener(eventName, () => galleryDropZone.classList.remove('drag-over'), false);
        });

        galleryDropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        });

        galleryInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            const validFiles = Array.from(files).filter(file => {
                if (!file.type.startsWith('image/')) {
                    alert(file.name + ' is not an image.');
                    return false;
                }
                if (file.size > 5 * 1024 * 1024) {
                    alert(file.name + ' exceeds 5MB size limit.');
                    return false;
                }
                return true;
            });

            if (newGalleryFiles.length + validFiles.length > maxGalleryImages) {
                alert('You can only upload up to ' + maxGalleryImages + ' images.');
                return;
            }

            validFiles.forEach(file => {
                newGalleryFiles.push(file);
                previewNewImage(file);
            });
        }

        function previewNewImage(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const item = document.createElement('div');
                item.className = 'new-gallery-item';
                item.innerHTML = '<img src="' + e.target.result + '" alt="' + file.name + '"><button type="button" class="remove-new-btn" title="Remove"><i class="fas fa-times"></i></button>';
                item.querySelector('.remove-new-btn').addEventListener('click', function() {
                    item.remove();
                    newGalleryFiles = newGalleryFiles.filter(f => f !== file);
                });
                newGalleryPreview.appendChild(item);
            };
            reader.readAsDataURL(file);
        }
    }

    // ========== AJAX FORM SUBMISSION ==========
    function displayAjaxErrors(errors) {
        var existingAlert = document.querySelector('.ajax-validation-alert');
        if (existingAlert) existingAlert.remove();
        document.querySelectorAll('.ajax-field-error').forEach(function(el) { el.remove(); });
        document.querySelectorAll('.is-invalid-ajax').forEach(function(el) { el.classList.remove('is-invalid-ajax'); });

        var totalErrors = 0;
        Object.keys(errors).forEach(function(field) {
            totalErrors += errors[field].length;
        });
        if (totalErrors === 0) return;

        var form = document.querySelector('form[method="POST"]');
        if (!form) return;
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible ajax-validation-alert';
        alertDiv.innerHTML = '<button type="button" class="close" data-dismiss="alert">&times;</button><h5><i class="icon fas fa-ban"></i> Validation Error!</h5><p>Please fix the following errors and try again.</p><ul>';
        Object.keys(errors).forEach(function(field) {
            errors[field].forEach(function(msg) {
                alertDiv.innerHTML += '<li>' + msg + '</li>';
            });
        });
        alertDiv.innerHTML += '</ul>';
        form.insertBefore(alertDiv, form.firstChild);

        Object.keys(errors).forEach(function(field) {
            var input = form.querySelector('[name="' + field + '"]');
            if (input) {
                input.classList.add('is-invalid-ajax');
                var errorSpan = document.createElement('span');
                errorSpan.className = 'text-danger ajax-field-error';
                errorSpan.style.fontSize = '0.875em';
                errorSpan.textContent = errors[field][0];
                input.parentNode.appendChild(errorSpan);
            }
        });
    }

    const productForm = document.querySelector('form[method="POST"]');
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            e.preventDefault();

            var submitBtn = productForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
            }

            var prevAlert = productForm.querySelector('.ajax-validation-alert');
            if (prevAlert) prevAlert.remove();
            productForm.querySelectorAll('.ajax-field-error').forEach(function(el) { el.remove(); });
            productForm.querySelectorAll('.is-invalid-ajax').forEach(function(el) { el.classList.remove('is-invalid-ajax'); });

            var formData = new FormData(productForm);

            $.ajax({
                url: productForm.action,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (typeof Toast !== 'undefined') {
                        Toast.success(response.message || 'Product created successfully!');
                    }
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Create';
                    }
                    if (response.redirect) {
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 4500);
                    }
                },
                error: function(xhr) {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Create';
                    }
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON ? xhr.responseJSON.errors : {};
                        displayAjaxErrors(errors);
                        if (typeof Toast !== 'undefined') {
                            Toast.error('Please fix the validation errors and try again.');
                        }
                    } else {
                        var errorMsg = 'An error occurred. Please try again.';
                        if (typeof Toast !== 'undefined') {
                            Toast.error(errorMsg);
                        } else {
                            alert(errorMsg);
                        }
                    }
                }
            });
        });
    }

    // ========== PRODUCT VARIANTS ==========
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const productTypeSelect = document.getElementById('product_type');
    const variableFields = document.getElementById('variable-fields');
    const generateVariantsBtn = document.getElementById('generate-variants');
    const variantsContainer = document.getElementById('variants-container');
    const variantsTableBody = document.getElementById('variants-table-body');
    const variantsTable = document.getElementById('variants-table');
    const attributes = @json($attributes ?? []);

    function getSelectedAttributeValues() {
        var result = {};
        document.querySelectorAll('.attr-group').forEach(function(group) {
            var attrName = group.getAttribute('data-attr-name');
            var values = [];
            group.querySelectorAll('.attr-option.selected').forEach(function(opt) {
                values.push(opt.getAttribute('data-value'));
            });
            if (values.length > 0) {
                result[attrName] = values;
            }
        });
        return result;
    }

    function updateSelectedCounts() {
        document.querySelectorAll('.attr-group').forEach(function(group) {
            var sel = group.querySelectorAll('.attr-option.selected').length;
            var label = group.querySelector('.attr-selected-count');
            if (label) label.textContent = sel + ' selected';
        });
    }

    function toggleAttributeOption(btn) {
        btn.classList.toggle('selected');
        updateSelectedCounts();
        updateCombinationPreview();
        if (variantsContainer) variantsContainer.style.display = 'none';
        if (variantsTableBody) variantsTableBody.innerHTML = '';
    }

    document.querySelectorAll('.attr-option').forEach(function(btn) {
        btn.addEventListener('click', function() {
            toggleAttributeOption(this);
        });
    });

    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            slugInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        });
    }

    if (productTypeSelect && variableFields) {
        productTypeSelect.addEventListener('change', function() {
            if (this.value === 'variable') {
                variableFields.style.display = 'block';
            } else {
                variableFields.style.display = 'none';
                variantsContainer.style.display = 'none';
            }
        });
    }

    // ========== VARIANT TAB VISIBILITY ==========
    var vn = document.getElementById('variants-tab-nav');
    var varBtn = document.getElementById('action-create-variant');
    if (productTypeSelect && vn) {
        function toggleVariantTab() {
            vn.style.display = productTypeSelect.value === 'variable' ? '' : 'none';
        }
        productTypeSelect.addEventListener('change', toggleVariantTab);
        toggleVariantTab();
    }
    if (varBtn && productTypeSelect) {
        productTypeSelect.addEventListener('change', function() {
            varBtn.style.display = this.value === 'variable' ? 'inline-block' : 'none';
        });
        varBtn.style.display = productTypeSelect.value === 'variable' ? 'inline-block' : 'none';
    }

    // ========== COMBINATION PREVIEW ==========
    const comboPreview = document.getElementById('combination-preview');
    const previewBody = document.getElementById('preview-body');
    const previewCount = document.getElementById('preview-count');

    function updateCombinationPreview() {
        var selectedValues = getSelectedAttributeValues();
        var combos = generateCombinations(selectedValues);
        var keys = Object.keys(selectedValues);

        if (keys.length === 0 || combos.length === 0) {
            comboPreview.style.display = 'none';
            return;
        }

        comboPreview.style.display = 'block';
        var html = '';
        combos.forEach(function(combo) {
            var label = Object.values(combo).join(' - ');
            var comboJson = JSON.stringify(combo).replace(/'/g, '&#39;');
            html += `<label class="combo-checkbox-label checked"><input type="checkbox" class="combo-cb" checked value='${comboJson}' /> ${label}</label>`;
        });
        previewBody.innerHTML = html;
        previewCount.textContent = combos.length + ' combinations';
    }

    if (generateVariantsBtn) {
        console.log('Generate Variants button found');
        generateVariantsBtn.addEventListener('click', function() {
            // Set product type to variable
            if (productTypeSelect) {
                productTypeSelect.value = 'variable';
                if (typeof jQuery !== 'undefined') {
                    $(productTypeSelect).trigger('change');
                }
            }
            // Manually show variable fields
            if (variableFields) {
                variableFields.style.display = 'block';
            }
            // Generate combinations and render variants
            if (comboPreview.style.display !== 'none' && previewBody) {
                var filtered = [];
                previewBody.querySelectorAll('.combo-cb:checked').forEach(function(cb) {
                    var raw = cb.getAttribute('value');
                    if (raw) {
                        try {
                            filtered.push(JSON.parse(raw.replace(/&#39;/g, "'")));
                        } catch(e) {}
                    }
                });
                renderVariants(filtered);
            } else {
                var combinations = generateCombinations(getSelectedAttributeValues());
                if (combinations.length > 0) {
                    renderVariants(combinations);
                } else {
                    alert('Please select at least one attribute value from each attribute group before generating variants.');
                    return;
                }
            }
        });
    }

    function generateCombinations(selectedValues) {
        const keys = Object.keys(selectedValues);
        if (keys.length === 0) return [];
        const result = [];
        const valueArrays = keys.map(key => selectedValues[key]);
        const total = valueArrays.reduce((acc, arr) => acc * arr.length, 1);
        for (let i = 0; i < total; i++) {
            const combo = {};
            let divisor = total;
            keys.forEach(function(key, idx) {
                divisor = divisor / valueArrays[idx].length;
                combo[key] = valueArrays[idx][Math.floor((i / divisor) % valueArrays[idx].length)];
            });
            result.push(combo);
        }
        return result;
    }

    function renderVariants(combinations) {
        if (combinations.length === 0) {
            variantsContainer.style.display = 'none';
            return;
        }
        variantsContainer.style.display = 'block';

        // Update table header to match the actual attribute columns used in the combinations.
        // The header is initially built from ALL system attributes, but only the selected
        // attributes are used in the variants.
        if (variantsTable) {
            var headerRow = variantsTable.querySelector('thead tr');
            if (headerRow) {
                // Clone static (non-attribute) header cells so we can rebuild safely
                var staticHeaders = [];
                var headerThs = headerRow.querySelectorAll('th');
                headerThs.forEach(function(th) {
                    if (!th.hasAttribute('data-attr-name')) {
                        staticHeaders.push(th.cloneNode(true));
                    }
                });
                // Rebuild header row with attribute columns from the first combination
                headerRow.innerHTML = '';
                var firstCombo = combinations[0];
                Object.keys(firstCombo).forEach(function(attrName) {
                    var th = document.createElement('th');
                    th.setAttribute('data-attr-name', attrName);
                    th.textContent = attrName;
                    headerRow.appendChild(th);
                });
                staticHeaders.forEach(function(th) {
                    headerRow.appendChild(th);
                });
            }
        }

        var index = 0;
        combinations.forEach(function(combo) {
            var tr = document.createElement('tr');
            Object.keys(combo).forEach(function(key) {
                var td = document.createElement('td');
                td.textContent = combo[key];
                td.setAttribute('data-attr-name', key);
                tr.appendChild(td);
            });
            var imgTd = document.createElement('td');
            imgTd.innerHTML = '<small class="text-muted">No variant images</small>';
            tr.appendChild(imgTd);
            var skuTd = document.createElement('td');
            skuTd.innerHTML = '<input type="text" name="variants[' + index + '][sku]" class="form-control" placeholder="SKU" />';
            tr.appendChild(skuTd);
            var priceTd = document.createElement('td');
            priceTd.innerHTML = '<input type="number" step="0.01" name="variants[' + index + '][price]" class="form-control" placeholder="0.00" />';
            tr.appendChild(priceTd);
            var stockTd = document.createElement('td');
            stockTd.innerHTML = '<input type="number" name="variants[' + index + '][stock]" class="form-control" value="0" placeholder="0" />';
            tr.appendChild(stockTd);
            var actionTd = document.createElement('td');
            actionTd.innerHTML = '<button type="button" class="btn btn-danger btn-sm remove-variant">Remove</button>';
            tr.appendChild(actionTd);
            var nameInput2 = document.createElement('input');
            nameInput2.type = 'hidden';
            nameInput2.name = 'variants[' + index + '][name]';
            nameInput2.value = Object.values(combo).join(' - ');
            tr.appendChild(nameInput2);
            var attrInput = document.createElement('input');
            attrInput.type = 'hidden';
            attrInput.name = 'variants[' + index + '][attributes]';
            attrInput.value = JSON.stringify(combo);
            tr.appendChild(attrInput);
            variantsTableBody.appendChild(tr);
            index++;
        });
        if (productTypeSelect && productTypeSelect.value === 'variable') {
            variableFields.style.display = 'block';
        }
    }

    if (variantsTableBody) {
        variantsTableBody.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-variant')) {
                e.target.closest('tr').remove();
            }
        });
    }

    // ========== SCROLL-SPY TAB NAVIGATION ==========
    (function() {
        var tabLinks = document.querySelectorAll('#productFormTabs .nav-link');
        var sections = [];
        tabLinks.forEach(function(link) {
            var href = link.getAttribute('href');
            if (href && href.charAt(0) === '#') {
                var el = document.getElementById(href.substring(1));
                if (el) sections.push({ el: el, link: link });
            }
        });

        function updateActiveTab() {
            var scrollPos = window.scrollY + 120;
            var activeId = null;
            sections.forEach(function(item) {
                var top = item.el.offsetTop;
                var bottom = top + item.el.offsetHeight;
                if (scrollPos >= top && scrollPos < bottom) {
                    activeId = item.link.getAttribute('href').substring(1);
                }
            });
            tabLinks.forEach(function(link) {
                var id = link.getAttribute('href').substring(1);
                link.classList.toggle('active', id === activeId);
            });
        }

        tabLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var target = document.getElementById(this.getAttribute('href').substring(1));
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        window.addEventListener('scroll', function() {
            updateActiveTab();
        });
        updateActiveTab();
    })();
});
</script>