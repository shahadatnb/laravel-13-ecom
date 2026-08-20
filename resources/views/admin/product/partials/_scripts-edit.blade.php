<script>
document.addEventListener('DOMContentLoaded', function() {
    const productId = @json($product->id);
    const csrfToken = '{{ csrf_token() }}';

    // ========== THUMBNAIL UPLOAD (Edit - Immediate AJAX Upload) ==========
    const thumbnailInput = document.getElementById('thumbnail');
    const thumbnailUploadBtn = document.getElementById('thumbnail-upload-btn');
    const thumbnailPreviewBox = document.getElementById('thumbnail-preview-box');
    const thumbnailUploadStatus = document.getElementById('thumbnail-upload-status');

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
                    thumbnailInput.value = '';
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size should not exceed 5MB.');
                    thumbnailInput.value = '';
                    return;
                }

                // Show upload status
                if (thumbnailUploadStatus) thumbnailUploadStatus.style.display = 'block';

                const formData = new FormData();
                formData.append('thumbnail', file);
                formData.append('_token', csrfToken);

                $.ajax({
                    url: '{{ route("admin.product.upload-thumbnail", $product->id) }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (thumbnailUploadStatus) thumbnailUploadStatus.style.display = 'none';
                        if (typeof Toast !== 'undefined') {
                            Toast.success('Thumbnail uploaded successfully!');
                        }
                        // Reload page to show new thumbnail
                        setTimeout(function() {
                            window.location.reload();
                        }, 4000);
                    },
                    error: function(xhr) {
                        if (thumbnailUploadStatus) thumbnailUploadStatus.style.display = 'none';
                        thumbnailInput.value = '';
                        if (typeof Toast !== 'undefined') {
                            Toast.error('Failed to upload thumbnail.');
                        } else {
                            alert('Failed to upload thumbnail.');
                        }
                    }
                });
            }
        });
    }

    // Remove thumbnail via AJAX
    const thumbnailRemoveBtn = document.getElementById('thumbnail-remove-btn');
    if (thumbnailRemoveBtn) {
        thumbnailRemoveBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove the thumbnail?')) {
                $.ajax({
                    url: '{{ route("admin.product.remove-thumbnail", $product->id) }}',
                    method: 'DELETE',
                    data: { _token: csrfToken },
                    success: function(response) {
                        if (typeof Toast !== 'undefined') {
                            Toast.success('Thumbnail removed successfully!');
                        }
                        setTimeout(function() {
                            window.location.reload();
                        }, 4000);
                    },
                    error: function(xhr) {
                        if (typeof Toast !== 'undefined') {
                            Toast.error('Failed to remove thumbnail.');
                        } else {
                            alert('Failed to remove thumbnail.');
                        }
                    }
                });
            }
        });
    }

    // ========== GALLERY UPLOAD (Edit - Immediate AJAX Upload) ==========
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
                uploadGalleryImage(file);
            });
        }

        function uploadGalleryImage(file) {
            const formData = new FormData();
            formData.append('images[]', file);
            formData.append('_token', csrfToken);

            // Create temporary preview with loading indicator
            const tempItem = document.createElement('div');
            tempItem.className = 'new-gallery-item loading';
            tempItem.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
            if (newGalleryPreview) newGalleryPreview.appendChild(tempItem);

            $.ajax({
                url: '{{ route("admin.product.upload-gallery", $product->id) }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    tempItem.remove();
                    if (typeof Toast !== 'undefined') {
                        Toast.success('Image uploaded successfully!');
                    }
                    // Reload to show new image in gallery
                    setTimeout(function() {
                        window.location.reload();
                    }, 4000);
                },
                error: function(xhr) {
                    tempItem.remove();
                    newGalleryFiles = newGalleryFiles.filter(f => f !== file);
                    if (typeof Toast !== 'undefined') {
                        Toast.error('Failed to upload image.');
                    } else {
                        alert('Failed to upload image.');
                    }
                }
            });
        }
    }

    // Delete gallery image via AJAX
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const imageId = btn.getAttribute('data-image-id');
            const imagePath = btn.getAttribute('data-image-path');

            if (confirm('Are you sure you want to delete this image permanently?')) {

                $.ajax({
                    url: '{{ route("admin.product.delete-gallery", $product->id) }}',
                    method: 'POST',
                    data: {
                        '_token': csrfToken,
                        'image_id': imageId,
                        'image_path': imagePath
                    },
                    success: function(response) {
                        const galleryItem = btn.closest('.gallery-item');
                        if (galleryItem) {
                            galleryItem.style.transition = 'all 0.3s ease';
                            galleryItem.style.opacity = '0';
                            galleryItem.style.transform = 'scale(0.8)';
                            setTimeout(function() {
                                galleryItem.remove();
                            }, 300);
                        }
                        if (typeof Toast !== 'undefined') {
                            Toast.success('Image deleted successfully!');
                        }
                    },
                    error: function(xhr) {
                        if (typeof Toast !== 'undefined') {
                            Toast.error('Failed to delete image.');
                        } else {
                            alert('Failed to delete image.');
                        }
                    }
                });
            }
        }
    });

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
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            }

            var prevAlert = productForm.querySelector('.ajax-validation-alert');
            if (prevAlert) prevAlert.remove();
            productForm.querySelectorAll('.ajax-field-error').forEach(function(el) { el.remove(); });
            productForm.querySelectorAll('.is-invalid-ajax').forEach(function(el) { el.classList.remove('is-invalid-ajax'); });

            var method = productForm.querySelector('input[name="_method"]');
            var httpMethod = method ? method.value : 'PUT';

            var formData = new FormData(productForm);

            $.ajax({
                url: productForm.action,
                method: httpMethod,
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (typeof Toast !== 'undefined') {
                        Toast.success(response.message || 'Product updated successfully!');
                    }
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Update';
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
                        submitBtn.innerHTML = 'Update';
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
    const existingVariants = @json($product->variants ?? []);

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
            html += '<label class="combo-checkbox-label checked"><input type="checkbox" class="combo-cb" checked data-combo="' + comboJson + '" /> ' + label + '</label>';
        });
        previewBody.innerHTML = html;
        previewCount.textContent = combos.length + ' combinations';
    }

    if (generateVariantsBtn) {
        generateVariantsBtn.addEventListener('click', function() {
            if (productTypeSelect) {
                productTypeSelect.value = 'variable';
                if (typeof jQuery !== 'undefined') {
                    $(productTypeSelect).trigger('change');
                }
            }
            if (comboPreview.style.display !== 'none' && previewBody) {
                var filtered = [];
                previewBody.querySelectorAll('.combo-cb:checked').forEach(function(cb) {
                    var raw = cb.getAttribute('data-combo');
                    if (raw) {
                        try {
                            filtered.push(JSON.parse(raw.replace(/&#39;/g, "'")));
                        } catch(e) {}
                    }
                });
                renderVariants(filtered);
            } else {
                renderVariants(generateCombinations(getSelectedAttributeValues()));
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

    // ========== LOAD EXISTING VARIANTS ON EDIT PAGE ==========
    if (productTypeSelect && productTypeSelect.value === 'variable' && existingVariants && existingVariants.length > 0) {
        // Convert existing variants to combinations format
        const combinations = existingVariants.map(function(v) {
            return JSON.parse(v.attributes);
        });
        renderVariants(combinations);
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