@extends('admin.layouts.app')
@section('title', 'Edit Product')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Product</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.product.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')
                @include('admin.product.partials._form-header')
                @include('admin.product.partials._basic-info')
                @include('admin.product.partials._pricing')
                @include('admin.product.partials._variants')
                @include('admin.product.partials._images-edit')
                @include('admin.product.partials._seo')

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    @include('admin.product.partials._styles')
@endpush

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productId = @json($product->id);
    const csrfToken = '{{ csrf_token() }}';
    const productTypeSelect = document.getElementById('product_type');

    // ========== THUMBNAIL UPLOAD ==========
    const thumbnailInput = document.getElementById('thumbnail');
    const thumbnailUploadBtn = document.getElementById('thumbnail-upload-btn');
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
                        Toast.success('Thumbnail uploaded successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 4000);
                    },
                    error: function(xhr) {
                        if (thumbnailUploadStatus) thumbnailUploadStatus.style.display = 'none';
                        thumbnailInput.value = '';
                        Toast.error('Failed to upload thumbnail.');
                    }
                });
            }
        });
    }

    // Remove thumbnail
    const thumbnailRemoveBtn = document.getElementById('thumbnail-remove-btn');
    if (thumbnailRemoveBtn) {
        thumbnailRemoveBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove the thumbnail?')) {
                $.ajax({
                    url: '{{ route("admin.product.remove-thumbnail", $product->id) }}',
                    method: 'DELETE',
                    data: { _token: csrfToken },
                    success: function(response) {
                        Toast.success('Thumbnail removed successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 4000);
                    },
                    error: function(xhr) {
                        Toast.error('Failed to remove thumbnail.');
                    }
                });
            }
        });
    }

    // ========== GALLERY UPLOAD ==========
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
            handleFiles(e.dataTransfer.files);
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
                    Toast.success('Image uploaded successfully!');
                    setTimeout(function() {
                        window.location.reload();
                    }, 4000);
                },
                error: function(xhr) {
                    tempItem.remove();
                    newGalleryFiles = newGalleryFiles.filter(f => f !== file);
                    Toast.error('Failed to upload image.');
                }
            });
        }
    }

    // Delete gallery image
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
                        Toast.success('Image deleted successfully!');
                    },
                    error: function(xhr) {
                        Toast.error('Failed to delete image.');
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
                    Toast.success(response.message || 'Product updated successfully!');
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
                        Toast.error('Please fix the validation errors and try again.');
                    } else {
                        Toast.error('An error occurred. Please try again.');
                    }
                }
            });
        });
    }

    // ========== PRODUCT VARIANTS ==========
    const variableFields = document.getElementById('variable-fields');
    const generateVariantsBtn = document.getElementById('generate-variants');
    const variantsContainer = document.getElementById('variants-container');
    const variantsTableBody = document.getElementById('variants-table-body');
    const variantsTable = document.getElementById('variants-table');
    const attributes = @json($attributes ?? []);
    var currentVariants = [];

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

    if (productTypeSelect && variableFields) {
        function checkProductType() {
            if (productTypeSelect.value === 'variable') {
                variableFields.style.display = 'block';
                // লোড ভ্যারিয়েন্টস ফাংশন কল
                loadVariants();
            } else {
                variableFields.style.display = 'none';
                variantsContainer.style.display = 'none';
            }
        }

        productTypeSelect.addEventListener('change', checkProductType);
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
        generateVariantsBtn.addEventListener('click', function() {
            var selectedCombinations = [];
            if (comboPreview.style.display !== 'none' && previewBody) {
                previewBody.querySelectorAll('.combo-cb:checked').forEach(function(cb) {
                    var raw = cb.getAttribute('value');
                    if (raw) {
                        try {
                            selectedCombinations.push(JSON.parse(raw.replace(/&#39;/g, "'")));
                        } catch(e) {}
                    }
                });
            } else {
                selectedCombinations = generateCombinations(getSelectedAttributeValues());
            }
            // Send to backend to save variants
            saveVariantsToBackend(selectedCombinations);
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

    // Save variants to backend via AJAX
    function saveVariantsToBackend(combinations) {
        // Check for duplicates against already-loaded variants before sending to backend.
        // Same variant = same set of attribute key-value pairs (order-independent).
        var newCombinations = [];
        var duplicateNames = [];
        combinations.forEach(function(combo) {
            var isDuplicate = currentVariants.some(function(v) {
                var stored = v.attributes || {};
                var storedKeys = Object.keys(stored).sort();
                var comboKeys = Object.keys(combo).sort();
                if (storedKeys.length !== comboKeys.length) return false;
                for (var i = 0; i < storedKeys.length; i++) {
                    if (storedKeys[i] !== comboKeys[i]) return false;
                    if (stored[storedKeys[i]] !== combo[comboKeys[i]]) return false;
                }
                return true;
            });
            if (isDuplicate) {
                duplicateNames.push(Object.values(combo).join(' - '));
            } else {
                newCombinations.push(combo);
            }
        });

        if (newCombinations.length === 0) {
            Toast.warning('All selected variants already exist. No new variants were added.');
            return;
        }

        if (duplicateNames.length > 0) {
            Toast.warning(duplicateNames.length + ' variant(s) already existed and were skipped: ' + duplicateNames.join(', '));
        }

        // Get product's SKU and price for autofilling new variants
        var productSku = '{{ $product->sku ?? '' }}';
        var productPrice = '{{ $product->sale_price ?? $product->regular_price ?? 0 }}';

        console.log('Saving variants to backend:', newCombinations);
        var variantsData = newCombinations.map(function(combo) {
            return {
                attributes: JSON.stringify(combo),
                name: Object.values(combo).join(' - '),
                sku: productSku,
                price: productPrice,
                stock: 0
            };
        });
        $.ajax({
            url: '{{ route("admin.product.variants-generate", $product->id) }}',
            method: 'POST',
            data: {
                _token: csrfToken,
                variants: variantsData
            },
             success: function(response) {
                var msg = response.message || 'Variants saved successfully!';
                Toast.success(msg, 'Success!', 6000);
                if (response.skipped_count > 0) {
                    Toast.warning(response.skipped_count + ' variant(s) already existed and were skipped.', 'Duplicate', 6000);
                }
                // Reload variants from database
                loadVariants();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : {};
                var errorMsg = 'An error occurred. Please try again.';
                if (Object.keys(errors).length > 0) {
                    errorMsg = Object.values(errors)[0][0];
                }
                Toast.error(errorMsg, 'Error!', 8000);
            }
        });
    }

    // Load variants from backend via AJAX
    function loadVariants() {
        if (!productTypeSelect || productTypeSelect.value !== 'variable') {
            return;
        }

        $.ajax({
            url: '{{ route("admin.product.variants-list", $product->id) }}',
            method: 'GET',
        success: function(response) {
                if (response.success && response.data) {
                    currentVariants = response.data;
                    renderVariantsFromBackend(response.data);
                }
            },
            error: function(xhr) {
                console.error('Failed to load variants:', xhr);
            }
        });
    }

    // Render variants from backend data
    function renderVariantsFromBackend(variants) {
        if (!variantsTableBody) return;

        variantsTableBody.innerHTML = '';

        if (!variants || variants.length === 0) {
            if (variantsContainer) variantsContainer.style.display = 'none';
            return;
        }

        if (variantsContainer) variantsContainer.style.display = 'block';

        // Update table header: keep only attribute columns that match the loaded variants' actual attributes.
        // The header is built from ALL system attributes in the Blade template, but a product variant
        // may only use a subset (e.g. Size, Color but not Material). Rebuild the header to match.
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
                // Determine unique attribute names from all variants
                var allAttrNames = [];
                var seenNames = {};
                variants.forEach(function(v) {
                    var attrs = v.attributes || {};
                    Object.keys(attrs).forEach(function(name) {
                        if (!seenNames[name]) {
                            seenNames[name] = true;
                            allAttrNames.push(name);
                        }
                    });
                });
                // Rebuild header row: attribute columns first, then static columns
                headerRow.innerHTML = '';
                allAttrNames.forEach(function(name) {
                    var th = document.createElement('th');
                    th.setAttribute('data-attr-name', name);
                    th.textContent = name;
                    headerRow.appendChild(th);
                });
                staticHeaders.forEach(function(th) {
                    headerRow.appendChild(th);
                });
            }
        }

        variants.forEach(function(variant, index) {
            var combo = variant.attributes;
            var tr = document.createElement('tr');
            tr.setAttribute('data-variant-id', variant.id);

            // Attribute columns
            Object.keys(combo).forEach(function(key) {
                var td = document.createElement('td');
                td.textContent = combo[key];
                td.setAttribute('data-attr-name', key);
                tr.appendChild(td);
            });

            // Image column
            var imgTd = document.createElement('td');
            if (variant.images && variant.images.length > 0) {
                var img = variant.images[0];
                imgTd.innerHTML = '<img src="' + asset('storage/' + img.image) + '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" />' +
                    '<button type="button" class="btn btn-xs btn-danger mt-1 remove-variant-img" data-variant-id="' + variant.id + '">Remove</button>';
            } else {
                imgTd.innerHTML = '<small class="text-muted">No image</small>' +
                    '<button type="button" class="btn btn-xs btn-primary mt-1 upload-variant-img" data-variant-id="' + variant.id + '">Upload</button>' +
                    '<input type="file" class="variant-img-input" data-variant-id="' + variant.id + '" style="display: none;" accept="image/*" />';
            }
            tr.appendChild(imgTd);

            // SKU
            var skuTd = document.createElement('td');
            skuTd.innerHTML = '<input type="text" name="variants[' + index + '][sku]" class="form-control form-control-sm" value="' + (variant.sku || '') + '" placeholder="SKU" />';
            tr.appendChild(skuTd);

            // Price
            var priceTd = document.createElement('td');
            priceTd.innerHTML = '<input type="number" step="0.01" name="variants[' + index + '][price]" class="form-control form-control-sm" value="' + (variant.price || '0.00') + '" placeholder="0.00" />';
            tr.appendChild(priceTd);

// Stock (clickable — managed via Stock module)
var stockTd = document.createElement('td');
var stockLink = stockTd.appendChild(document.createElement('a'));
stockLink.href = '{{ route("admin.stock.variant-show", [$product->id, "__VARIANT_ID__"]) }}'.replace('__VARIANT_ID__', variant.id);
stockLink.className = 'text-primary font-weight-bold';
stockLink.title = 'Manage stock for this variant';
stockLink.textContent = variant.stock || '0';
tr.appendChild(stockTd);

            // Action
            var actionTd = document.createElement('td');
            actionTd.innerHTML = '<button type="button" class="btn btn-danger btn-sm remove-variant" data-variant-id="' + variant.id + '">Remove</button>';
            tr.appendChild(actionTd);

            // Hidden inputs
            var nameInput = document.createElement('input');
            nameInput.type = 'hidden';
            nameInput.name = 'variants[' + index + '][name]';
            nameInput.value = variant.name || Object.values(combo).join(' - ');
            tr.appendChild(nameInput);

            var attrInput = document.createElement('input');
            attrInput.type = 'hidden';
            attrInput.name = 'variants[' + index + '][attributes]';
            attrInput.value = JSON.stringify(combo);
            tr.appendChild(attrInput);

            var idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'variants[' + index + '][id]';
            idInput.value = variant.id;
            tr.appendChild(idInput);

            variantsTableBody.appendChild(tr);
        });
    }

    // Helper function to get asset URL
    function asset(path) {
        return '{{ asset("") }}' + path;
    }

    // Handle variant table interactions
    if (variantsTableBody) {
        variantsTableBody.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-variant')) {
                var variantId = e.target.getAttribute('data-variant-id');
                if (variantId && confirm('Are you sure you want to remove this variant?')) {
                    $.ajax({
                        url: '{{ route("admin.product.variant-delete", $product->id) }}',
                        method: 'DELETE',
                        data: {
                            _token: csrfToken,
                            variant_id: variantId,
                        },
                        success: function(response) {
                            if (response.success) {
                                var row = e.target.closest('tr');
                                if (row) row.remove();
                                currentVariants = currentVariants.filter(function(v) {
                                    return String(v.id) !== String(variantId);
                                });
                                Toast.success('Variant removed successfully!');
                            }
                        },
                        error: function(xhr) {
                            Toast.error('Failed to remove variant.');
                        }
                    });
                }
            }
        });
    }

    // Auto-save variant field changes (sku, price, stock) via AJAX
    if (variantsTableBody) {
        variantsTableBody.addEventListener('change', function(e) {
            if (e.target.matches('input[name$="[sku]"]') ||
                e.target.matches('input[name$="[price]"]')) {
                var input = e.target;
                var tr = input.closest('tr');
                var variantId = tr.getAttribute('data-variant-id');
                var name = input.name;
                var value = input.value;
                var fieldName = '';

                if (name.includes('[sku]')) fieldName = 'sku';
                else if (name.includes('[price]')) fieldName = 'price';

                if (variantId && fieldName) {
                    $.ajax({
                        url: '{{ route("admin.product.variant-update", $product->id) }}',
                        method: 'POST',
                        data: {
                            _token: csrfToken,
                            variant_id: variantId,
                            field: fieldName,
                            value: value,
                        },
                        success: function(response) {
                            if (response.success) {
                                Toast.success('Variant updated successfully!');
                            }
                        },
                        error: function(xhr) {
                            Toast.error('Failed to update variant.');
                        }
                    });
                }
            }
        });
    }

    // Handle variant image upload and remove buttons
    if (variantsTableBody) {
        variantsTableBody.addEventListener('click', function(e) {
            // Upload variant image button: open file input
            if (e.target.classList.contains('upload-variant-img')) {
                var variantId = e.target.getAttribute('data-variant-id');
                var fileInput = variantsTableBody.querySelector('.variant-img-input[data-variant-id="' + variantId + '"]');
                if (fileInput) {
                    fileInput.click();
                }
            }

            // Remove variant image button: send AJAX delete
            if (e.target.classList.contains('remove-variant-img')) {
                var variantId = e.target.getAttribute('data-variant-id');
                if (variantId && confirm('Are you sure you want to remove this variant image?')) {
                    $.ajax({
                        url: '{{ route("admin.product.delete-variant-image", $product->id) }}',
                        method: 'POST',
                        data: {
                            _token: csrfToken,
                            _method: 'DELETE',
                            variant_id: variantId,
                        },
                        success: function(response) {
                            if (response.success) {
                                loadVariants();
                                Toast.success('Variant image removed successfully!');
                            }
                        },
                        error: function(xhr) {
                            Toast.error('Failed to remove variant image.');
                        }
                    });
                }
            }
        });

        // Handle file input change for variant image upload
        variantsTableBody.addEventListener('change', function(e) {
            if (e.target.classList.contains('variant-img-input')) {
                var fileInput = e.target;
                var variantId = fileInput.getAttribute('data-variant-id');
                var file = fileInput.files[0];

                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    Toast.error('Please select a valid image file.');
                    fileInput.value = '';
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    Toast.error('Image size should not exceed 5MB.');
                    fileInput.value = '';
                    return;
                }

                var formData = new FormData();
                formData.append('image', file);
                formData.append('variant_id', variantId);
                formData.append('_token', csrfToken);

                $.ajax({
                    url: '{{ route("admin.product.upload-variant-image", $product->id) }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            loadVariants();
                            Toast.success('Variant image uploaded successfully!');
                        }
                    },
                    error: function(xhr) {
                        Toast.error('Failed to upload variant image.');
                    }
                });

                fileInput.value = '';
            }
        });
    }

    // ========== LOAD EXISTING VARIANTS ON PAGE LOAD ==========
    // Wait for page to fully load, then load variants if product type is variable
    window.addEventListener('load', function() {
        if (productTypeSelect && productTypeSelect.value === 'variable') {
            loadVariants();
        }
    });

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

    checkProductType();
});
</script>
@endsection
