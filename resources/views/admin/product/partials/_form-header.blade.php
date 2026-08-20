@if(!empty($product))
    <form method="POST" action="{{ route('admin.product.update', $product->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
@else
    <form method="POST" action="{{ route('admin.product.store') }}" enctype="multipart/form-data">
        @csrf
@endif

{{-- Sticky Tab Navigation --}}
<ul class="nav nav-tabs" id="productFormTabs" role="tablist" style="position:sticky;top:0;z-index:100;background:#fff;padding-top:8px;margin-bottom:16px;border-bottom:2px solid #dee2e6;display:flex;flex-wrap:nowrap;overflow-x:auto;">
    <li class="nav-item">
        <a class="nav-link active" href="#section-basic" data-section="basic">
            <i class="fas fa-info-circle"></i> <span class="d-none d-md-inline">Basic Info</span><span class="d-inline d-md-none">Info</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#section-pricing" data-section="pricing">
            <i class="fas fa-dollar-sign"></i> <span class="d-none d-md-inline">Pricing & Stock</span><span class="d-inline d-md-none">Price</span>
        </a>
    </li>
    <li class="nav-item" id="variants-tab-nav" style="display:none;">
        <a class="nav-link" href="#section-variants" data-section="variants">
            <i class="fas fa-layer-group"></i> <span class="d-none d-md-inline">Variants</span><span class="d-inline d-md-none">Vars</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#section-images" data-section="images">
            <i class="fas fa-images"></i> <span class="d-none d-md-inline">Images</span><span class="d-inline d-md-none">Pics</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#section-seo" data-section="seo">
            <i class="fas fa-search"></i> <span class="d-none d-md-inline">SEO</span><span class="d-inline d-md-none">SEO</span>
        </a>
    </li>
</ul>

{{-- ===== STICKY ACTION TOOLBAR ===== --}}
<div class="product-action-toolbar d-none" id="productActionToolbar">
    <div class="action-toolbar-left">
        <span class="toolbar-label"><i class="fas fa-bolt text-warning"></i> Quick Actions</span>
    </div>
    <div class="action-toolbar-right">
        <button type="button" class="btn btn-sm btn-secondary" id="action-save-draft" title="Save as draft">
            <i class="fas fa-pen"></i> Save Draft
        </button>
        <button type="button" class="btn btn-sm btn-success" id="action-save-publish" title="Save and publish">
            <i class="fas fa-check-circle"></i> Save & Publish
        </button>
        <button type="button" class="btn btn-sm btn-primary" id="action-create-variant" style="display:none;" title="Scroll to variant editor">
            <i class="fas fa-layer-group"></i> Create Variants
        </button>
    </div>
</div>