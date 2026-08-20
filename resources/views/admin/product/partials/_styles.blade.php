<style>
/* Quick Action Toolbar */
.product-action-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 8px 14px;
    margin-bottom: 16px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 6px;
    position: sticky;
    top: 48px;
    z-index: 99;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}
.product-action-toolbar .toolbar-label {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.product-action-toolbar .action-toolbar-right {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.product-action-toolbar .btn {
    font-size: 12px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 4px;
    transition: all 0.15s ease;
}
.product-action-toolbar .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
#action-save-draft { border: 1px solid #ced4da; }
#action-save-publish { border: 1px solid #28a745; }
#action-save-continue { border: 1px solid #17a2b8; }

/* Tab Navigation */
#productFormTabs {
    background: #fff !important;
    scrollbar-width: thin;
}
#productFormTabs .nav-link {
    color: #6c757d;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 8px 16px;
    font-weight: 500;
    transition: all 0.2s ease;
    white-space: nowrap;
}
#productFormTabs .nav-link:hover {
    color: #007bff;
    background: #f8f9fa;
    border-bottom-color: #adb5bd;
}
#productFormTabs .nav-link.active {
    color: #007bff;
    background: transparent;
    border-bottom-color: #007bff;
    font-weight: 600;
}

/* Smooth scroll */
html {
    scroll-behavior: smooth;
}
[id^="section-"] {
    scroll-margin-top: 75px;
}

/* Product Images Section Styles */
.card-header.bg-gradient.bg-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
}

/* Thumbnail Upload Styles */
.thumbnail-upload-container {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 15px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}
.thumbnail-upload-container:hover {
    border-color: #007bff;
    background: #f0f7ff;
}
.thumbnail-preview-box {
    width: 100%;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 12px;
    position: relative;
}
.thumbnail-preview-box img {
    max-width: 50px;
    max-height: 50px;
    object-fit: contain;
}
.thumbnail-placeholder {
    text-align: center;
    color: #6c757d;
}
.thumbnail-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.thumbnail-preview-box:hover .thumbnail-overlay {
    opacity: 1;
}
.btn-remove-thumbnail {
    background: #dc3545;
    border: none;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}
.btn-remove-thumbnail:hover {
    background: #c82333;
    transform: scale(1.1);
}
.thumbnail-upload-btn {
    border-radius: 6px;
    padding: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
}
.thumbnail-upload-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

/* Gallery Drop Zone Styles */
.gallery-drop-zone {
    border: 3px dashed #dee2e6;
    border-radius: 12px;
    padding: 30px 20px;
    text-align: center;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}
.gallery-drop-zone:hover,
.gallery-drop-zone.drag-over {
    border-color: #28a745;
    background: #f0fff4;
    transform: scale(1.02);
}
.gallery-drop-zone.drag-over {
    animation: pulse 1s infinite;
}
@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4); }
    50% { box-shadow: 0 0 0 15px rgba(40, 167, 69, 0); }
}
.drop-zone-content i {
    color: #007bff;
    transition: all 0.3s ease;
}
.gallery-drop-zone:hover .drop-zone-content i,
.gallery-drop-zone.drag-over .drop-zone-content i {
    color: #28a745;
    transform: translateY(-5px);
}
.drop-zone-content p {
    margin: 0;
    color: #495057;
}
.drop-zone-content .badge {
    font-size: 0.75rem;
    padding: 6px 12px;
}

/* New Gallery Preview */
.new-gallery-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.new-gallery-item {
    position: relative;
    width: 100px;
    height: 100px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    animation: slideIn 0.3s ease;
}
@keyframes slideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.new-gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.new-gallery-item .remove-new-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 24px;
    height: 24px;
    background: #dc3545;
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all 0.2s ease;
}
.new-gallery-item .remove-new-btn:hover {
    background: #c82333;
    transform: scale(1.1);
}

/* Existing Gallery Container */
.existing-gallery-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}
.gallery-item {
    position: relative;
    width: 120px;
    height: 120px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
    cursor: pointer;
    transition: all 0.3s ease;
}
.gallery-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}
.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.gallery-item-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.gallery-item:hover .gallery-item-overlay {
    opacity: 1;
}
.gallery-item-actions {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
}
.gallery-item-index {
    position: absolute;
    bottom: 5px;
    left: 5px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 11px;
}

/* Attribute Selection Styles */
.attr-group {
    margin-bottom: 20px;
    padding: 15px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: #fafafa;
}
.attr-group-label {
    font-weight: 600;
    margin-bottom: 10px;
    display: block;
}
.attr-options {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.attr-option {
    padding: 8px 16px;
    border: 2px solid #dee2e6;
    border-radius: 20px;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 13px;
}
.attr-option:hover {
    border-color: #007bff;
    background: #f0f7ff;
}
.attr-option.selected {
    border-color: #28a745;
    background: #d4edda;
    color: #155724;
    font-weight: 600;
}
.attr-option.attr-swatch {
    width: 40px;
    height: 40px;
    padding: 0;
    border-radius: 50%;
    position: relative;
}
.attr-option.attr-swatch .swatch-label {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 9px;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    pointer-events: none;
}
.attr-option.attr-swatch.selected {
    box-shadow: 0 0 0 3px #28a745;
}

/* Combination Preview */
.combination-preview {
    margin-top: 20px;
    padding: 15px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: #f8f9fa;
}
.preview-header {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #dee2e6;
}
.preview-toggle-all input {
    margin-right: 5px;
}
.preview-body {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    max-height: 200px;
    overflow-y: auto;
}
.combo-checkbox-label {
    display: flex;
    align-items: center;
    padding: 6px 12px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
}
.combo-checkbox-label input {
    margin-right: 6px;
}
.combo-checkbox-label.checked {
    background: #d4edda;
    border-color: #28a745;
}

/* Variants Table */
#variants-table {
    font-size: 13px;
}
#variants-table th,
#variants-table td {
    vertical-align: middle;
}
#variants-table .form-control {
    font-size: 12px;
    padding: 4px 8px;
}

/* Bulk Update Bar */
.bulk-update-bar {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    margin-bottom: 15px;
    background: #f8f9fa;
}
.bulk-update-header {
    padding: 10px 15px;
    background: #e9ecef;
    border-bottom: 1px solid #dee2e6;
    cursor: pointer;
    display: flex;
    align-items: center;
    font-weight: 600;
    font-size: 13px;
}
.bulk-update-header:hover {
    background: #dee2e6;
}
.bulk-chevron {
    transition: transform 0.2s ease;
}
.bulk-update-body {
    padding: 15px;
}

/* Variant Summary Bar */
.variant-summary-bar {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    margin-bottom: 15px;
    color: white;
    flex-wrap: wrap;
    gap: 10px;
}
.summary-stat {
    display: flex;
    align-items: center;
    gap: 6px;
}
.summary-icon {
    font-size: 16px;
    opacity: 0.9;
}
.summary-value {
    font-weight: 700;
    font-size: 16px;
}
.summary-label {
    font-size: 11px;
    opacity: 0.9;
}
.summary-badge {
    padding: 2px 8px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 11px;
}
.summary-badge-instock {
    background: #28a745;
}
.summary-badge-low {
    background: #ffc107;
    color: #212529;
}
.summary-badge-out {
    background: #dc3545;
}
.summary-divider {
    width: 1px;
    height: 30px;
    background: rgba(255,255,255,0.3);
    margin: 0 10px;
}
.export-csv-btn {
    margin-left: auto;
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.3);
    color: white;
}
.export-csv-btn:hover {
    background: rgba(255,255,255,0.3);
    color: white;
}

/* Category Checkbox Tree */
.category-checkbox-tree label {
    cursor: pointer;
}
.category-checkbox-label:hover {
    background: #f8f9fa;
}

/* Validation Error Styles */
.is-invalid-ajax {
    border-color: #dc3545;
}
.is-invalid-ajax:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
.ajax-validation-alert {
    margin-bottom: 15px;
}
.ajax-field-error {
    display: block;
    margin-top: 4px;
}
</style>