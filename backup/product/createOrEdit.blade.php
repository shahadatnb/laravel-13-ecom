@extends('admin.layouts.app')
@section('title', empty($product) ? 'Create Product' : 'Edit Product')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ empty($product) ? 'Create Product' : 'Edit Product' }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.product.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')

                <form method="POST" action="{{ empty($product) ? route('admin.product.store') : route('admin.product.update', $product->id) }}" enctype="multipart/form-data">
                    @csrf
                    @if(!empty($product))
                        @method('PUT')
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
                                <i class="fas fa-check-circle"></i> Save &amp; Publish
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="action-create-variant" style="display:none;" title="Scroll to variant editor">
                                <i class="fas fa-layer-group"></i> Create Variants
                            </button>
                        </div>
                    </div>

                    {{-- ===== TAB: Basic Info ===== --}}
                    <div id="section-basic">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="product_type">Product Type <span class="text-danger">*</span></label>
                                <select id="product_type" name="product_type" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="simple" {{ old('product_type', $product->product_type ?? '') == 'simple' ? 'selected' : '' }}>Simple</option>
                                    <option value="variable" {{ old('product_type', $product->product_type ?? '') == 'variable' ? 'selected' : '' }}>Variable</option>
                                    <option value="digital" {{ old('product_type', $product->product_type ?? '') == 'digital' ? 'selected' : '' }}>Digital</option>
                                    <option value="service" {{ old('product_type', $product->product_type ?? '') == 'service' ? 'selected' : '' }}>Service</option>
                                    <option value="bundle" {{ old('product_type', $product->product_type ?? '') == 'bundle' ? 'selected' : '' }}>Bundle</option>
                                </select>
                                <small class="text-muted">Select "Variable" to add size, color etc.</small>
                                @error('product_type')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">Product Name <span class="text-danger">*</span></label>
                                <input id="name" name="name" type="text" value="{{ old('name', $product->name ?? '') }}" required class="form-control" placeholder="Product Name" />
                                @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name_bn">Name (Bengali)</label>
                                <input id="name_bn" name="name_bn" type="text" value="{{ old('name_bn', $product->name_bn ?? '') }}" class="form-control" placeholder="বাংলা নাম" />
                                @error('name_bn')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="slug">Slug <span class="text-danger">*</span></label>
                                <input id="slug" name="slug" type="text" value="{{ old('slug', $product->slug ?? '') }}" required class="form-control" placeholder="product-slug" />
                                @error('slug')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sku">SKU</label>
                                <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku ?? '') }}" class="form-control" placeholder="SKU-001" />
                                @error('sku')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="barcode">Barcode</label>
                                <input id="barcode" name="barcode" type="text" value="{{ old('barcode', $product->barcode ?? '') }}" class="form-control" placeholder="1234567890" />
                                @error('barcode')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="">Select Status</option>
                                    <option value="draft" {{ old('status', $product->status ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="pending" {{ old('status', $product->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="published" {{ old('status', $product->status ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="hidden" {{ old('status', $product->status ?? '') == 'hidden' ? 'selected' : '' }}>Hidden</option>
                                    <option value="archived" {{ old('status', $product->status ?? '') == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                @error('status')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="featured">Featured</label>
                                <select id="featured" name="featured" class="form-control">
                                    <option value="0" {{ old('featured', $product->featured ?? false) == false ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('featured', $product->featured ?? false) == true ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('featured')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="visibility">Visibility <span class="text-danger">*</span></label>
                                <select id="visibility" name="visibility" class="form-control" required>
                                    <option value="">Select Visibility</option>
                                    <option value="public" {{ old('visibility', $product->visibility ?? '') == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="private" {{ old('visibility', $product->visibility ?? '') == 'private' ? 'selected' : '' }}>Private</option>
                                    <option value="hidden" {{ old('visibility', $product->visibility ?? '') == 'hidden' ? 'selected' : '' }}>Hidden</option>
                                </select>
                                @error('visibility')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="brand_id">Brand</label>
                                <select id="brand_id" name="brand_id" class="form-control">
                                    <option value="">-- Select Brand --</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', isset($product) && $product->brand_id == $brand->id ? $product->brand_id : '') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category_id">
                                    <i class="fas fa-tag text-primary"></i>
                                    Primary Category
                                    <span class="text-muted font-weight-normal small">(main category)</span>
                                </label>
                                <select id="category_id" name="category_id" class="form-control">
                                    <option value="">-- Select Primary Category --</option>
                                    @foreach ($categoryTree as $cat)
                                        <option value="{{ $cat['id'] }}"
                                            {{ old('category_id', isset($product) && $product->category_id == $cat['id'] ? $product->category_id : '') == $cat['id'] ? 'selected' : '' }}>
                                            {{ str_repeat('— ', $cat['depth']) }}{{ $cat['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-tags text-success"></i>
                                    Additional Categories
                                    <span class="text-muted font-weight-normal small">(select multiple)</span>
                                </label>
                                <div class="category-checkbox-tree" style="max-height: 250px; overflow-y: auto; border: 1px solid #d2d6de; border-radius: 4px; padding: 10px; background: #fff;">
                                    @php
                                        $selectedCategoryIds = old('category_ids', isset($product) ? $product->categories->pluck('id')->toArray() : []);
                                    @endphp
                                    @foreach ($categoryTree as $cat)
                                        <label class="d-flex align-items-center py-1 category-checkbox-label" style="padding-left: {{ $cat['depth'] * 20 + 4 }}px;">
                                            <input
                                                type="checkbox"
                                                name="category_ids[]"
                                                value="{{ $cat['id'] }}"
                                                class="mr-2"
                                                {{ in_array($cat['id'], $selectedCategoryIds) ? 'checked' : '' }}
                                            />
                                            <span>
                                                @if($cat['depth'] === 0)
                                                    <strong>{{ $cat['name'] }}</strong>
                                                @else
                                                    <span class="text-muted small">—</span> {{ $cat['name'] }}
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <small class="text-muted">Primary category is included automatically. Check additional categories as needed.</small>
                                @error('category_ids')<span class="text-danger">{{ $message }}</span>@enderror
                                @error('category_ids.*')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    </div> {{-- /section-basic --}}

                    {{-- ===== TAB: Pricing & Stock ===== --}}
                    <div id="section-pricing">
                    <div class="card card-primary">
                        <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                            <h3 class="card-title"><i class="fas fa-dollar-sign"></i> Pricing & Stock</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body" id="pricingCardBody">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="regular_price">Regular Price</label>
                                        <input id="regular_price" name="regular_price" type="number" step="0.01" value="{{ old('regular_price', $product->regular_price ?? '') }}" class="form-control" placeholder="0.00" />
                                        @error('regular_price')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sale_price">Sale Price</label>
                                        <input id="sale_price" name="sale_price" type="number" step="0.01" value="{{ old('sale_price', $product->sale_price ?? '') }}" class="form-control" placeholder="0.00" />
                                        @error('sale_price')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cost_price">Cost Price</label>
                                        <input id="cost_price" name="cost_price" type="number" step="0.01" value="{{ old('cost_price', $product->cost_price ?? '') }}" class="form-control" placeholder="0.00" />
                                        @error('cost_price')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="stock">Stock</label>
                                        <input id="stock" name="stock" type="number" value="{{ old('stock', $product->stock ?? 0) }}" class="form-control" />
                                        @error('stock')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="minimum_stock">Minimum Stock</label>
                                        <input id="minimum_stock" name="minimum_stock" type="number" value="{{ old('minimum_stock', $product->minimum_stock ?? 0) }}" class="form-control" />
                                        @error('minimum_stock')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="maximum_order">Maximum Order</label>
                                        <input id="maximum_order" name="maximum_order" type="number" value="{{ old('maximum_order', $product->maximum_order ?? '') }}" class="form-control" />
                                        @error('maximum_order')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div> {{-- /section-pricing --}}

                    {{-- ===== TAB: Variants ===== --}}
                    <div id="section-variants">
                    <div id="variable-fields" class="row" style="display: none;">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Dynamic Variants</h3>
                                </div>
                                <div class="card-body">
                                    <div id="attributes-container">
                                        @foreach($attributes as $attribute)
                                            @if($attribute->type == 'select' || $attribute->type == 'color')
                                                <div class="form-group attr-group" data-attr-type="{{ $attribute->type }}" data-attr-name="{{ $attribute->name }}">
                                                    <label class="attr-group-label">
                                                        <i class="fas fa-{{ $attribute->type == 'color' ? 'palette' : 'ruler' }}"></i>
                                                        {{ $attribute->name }}
                                                        <span class="attr-selected-count text-muted small ml-2">0 selected</span>
                                                    </label>
                                                    <div class="attr-options">
                                                        @foreach($attribute->values as $value)
                                                            @if($attribute->type == 'color')
                                                                <button type="button"
                                                                    class="attr-option attr-swatch"
                                                                    data-attr-name="{{ $attribute->name }}"
                                                                    data-value="{{ $value->value }}"
                                                                    style="background-color: {{ $value->value }};"
                                                                    title="{{ $value->value }}">
                                                                    <span class="swatch-label">{{ $value->value }}</span>
                                                                </button>
                                                            @else
                                                                <button type="button"
                                                                    class="attr-option attr-pill"
                                                                    data-attr-name="{{ $attribute->name }}"
                                                                    data-value="{{ $value->value }}">
                                                                    {{ $value->value }}
                                                                </button>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    {{-- Combination Preview --}}
                                    <div id="combination-preview" class="combination-preview" style="display: none;">
                                        <div class="preview-header">
                                            <strong><i class="fas fa-list-check text-success"></i> Select Combinations</strong>
                                            <span class="text-muted small ml-2" id="preview-count">0 combinations</span>
                                            <label class="preview-toggle-all ml-auto mb-0">
                                                <input type="checkbox" id="preview-select-all" checked />
                                                <span class="small">All</span>
                                            </label>
                                        </div>
                                        <div class="preview-body" id="preview-body">
                                            {{-- Dynamically populated --}}
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-primary btn-sm mt-2" id="generate-variants">
                                        <i class="fas fa-plus"></i> Generate Selected
                                    </button>

                                    <div class="table-responsive mt-3" id="variants-container" style="display: none;">
                                        {{-- Bulk Update Toolbar --}}
                                        <div id="bulk-update-bar" class="bulk-update-bar">
                                            <div class="bulk-update-header" id="bulk-update-toggle">
                                                <i class="fas fa-layer-group text-info"></i>
                                                <strong>Bulk Update</strong>
                                                <span class="text-muted small ml-2">Set same value for all variants</span>
                                                <i class="fas fa-chevron-down ml-auto bulk-chevron"></i>
                                            </div>
                                            <div class="bulk-update-body" id="bulk-update-body" style="display: none;">
                                                <div class="row align-items-end">
                                                    <div class="col-md-3">
                                                        <div class="form-group mb-0">
                                                            <label class="small text-muted mb-1">SKU Prefix</label>
                                                            <div class="input-group input-group-sm">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                                                </div>
                                                                <input type="text" id="bulk-sku" class="form-control" placeholder="e.g. PROD-" />
                                                            </div>
                                                            <small class="text-muted" style="font-size: 10px;">Appended with attribute values</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group mb-0">
                                                            <label class="small text-muted mb-1">Price <span class="text-danger">*</span></label>
                                                            <div class="input-group input-group-sm">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span>
                                                                </div>
                                                                <input type="number" step="0.01" id="bulk-price" class="form-control" placeholder="0.00" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group mb-0">
                                                            <label class="small text-muted mb-1">Stock <span class="text-danger">*</span></label>
                                                            <div class="input-group input-group-sm">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fas fa-box"></i></span>
                                                                </div>
                                                                <input type="number" id="bulk-stock" class="form-control" placeholder="0" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group mb-0">
                                                            <label class="small text-muted mb-1">SKU Pattern</label>
                                                            <select id="bulk-sku-pattern" class="form-control form-control-sm">
                                                                <option value="prefix-attr">PREFIX-VALUE1-VALUE2</option>
                                                                <option value="attr-prefix">VALUE1-VALUE2-PREFIX</option>
                                                                <option value="prefix-only">PREFIX-1, PREFIX-2…</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" id="bulk-apply-btn" class="btn btn-info btn-block btn-sm">
                                                            <i class="fas fa-check-double"></i> Apply to All
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-12">
                                                        <small class="text-warning">
                                                            <i class="fas fa-info-circle"></i>
                                                            Fields left empty will be skipped (not overwritten).
                                                            Stock badges update live after apply.
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Summary Bar --}}
                                        <div class="variant-summary-bar" id="variant-summary-bar" style="display: none;">
                                            <div class="summary-stat">
                                                <span class="summary-icon summary-icon-total"><i class="fas fa-layer-group"></i></span>
                                                <span class="summary-value" id="summary-total">0</span>
                                                <span class="summary-label">Total Variants</span>
                                            </div>
                                            <div class="summary-divider"></div>
                                            <div class="summary-stat">
                                                <span class="summary-icon summary-icon-stock"><i class="fas fa-boxes"></i></span>
                                                <span class="summary-value" id="summary-total-stock">0</span>
                                                <span class="summary-label">Total Stock</span>
                                            </div>
                                            <div class="summary-divider"></div>
                                            <div class="summary-stat">
                                                <span class="summary-badge summary-badge-instock" id="summary-instock-count">0</span>
                                                <span class="summary-label">In Stock</span>
                                            </div>
                                            <div class="summary-stat">
                                                <span class="summary-badge summary-badge-low" id="summary-low-count">0</span>
                                                <span class="summary-label">Low</span>
                                            </div>
                                            <div class="summary-stat">
                                                <span class="summary-badge summary-badge-out" id="summary-out-count">0</span>
                                                <span class="summary-label">Out</span>
                                            </div>
                                            <div class="summary-divider"></div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary export-csv-btn" id="export-csv-btn" title="Download all variant data as CSV">
                                                <i class="fas fa-download"></i> Export CSV
                                            </button>
                                        </div>

                                        <table class="table table-bordered" id="variants-table">
<thead>
												 <tr>
													 @foreach($attributes as $attribute)
														 @if($attribute->type == 'select' || $attribute->type == 'color')
															 <th data-attr-name="{{ $attribute->name }}">{{ $attribute->name }}</th>
														 @endif
													 @endforeach
													 <th>
														Images
														<small class="d-block text-muted" style="font-size: 11px;">Upload variant-specific images</small>
													 </th>
													 <th>SKU</th>
													 <th>Price</th>
													 <th>Stock</th>
													 <th style="width: 50px;">
														<input type="checkbox" id="select-all-variants" checked title="Select/Deselect all" />
													 </th>
													 <th>Action</th>
												 </tr>
											 </thead>
                                            <tbody id="variants-table-body">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div> {{-- /section-variants --}}

                    {{-- ===== TAB: Images ===== --}}
                    <div id="section-images">
                    {{-- Product Images Section --}}
                    <div class="card card-secondary">
                        <div class="card-header bg-gradient bg-primary">
                            <h3 class="card-title">
                                <i class="fas fa-images mr-2"></i>Product Images
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- Thumbnail Upload --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="thumbnail" class="font-weight-bold">
                                            <i class="fas fa-camera text-primary"></i> Thumbnail Image
                                            <span class="text-danger">*</span>
                                        </label>
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-info-circle"></i> Recommended: 500x500px or higher
                                        </p>
                                        <div class="thumbnail-upload-container" id="thumbnail-container">
                                            <div class="thumbnail-preview-box" id="thumbnail-preview-box">
                                                @if(!empty($product) && $product->thumbnail && Storage::disk('public')->exists($product->thumbnail))
                                                    <img id="thumbnail-preview" src="{{ asset('storage/' . $product->thumbnail) }}" alt="Current Thumbnail" />
                                                    <div class="thumbnail-overlay" id="thumbnail-overlay">
                                                        <button type="button" class="btn-remove-thumbnail" id="thumbnail-remove-btn" title="Remove">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="thumbnail-placeholder">
                                                        <i class="fas fa-image fa-3x text-muted"></i>
                                                        <p class="mt-2 mb-0 text-muted">No thumbnail selected</p>
                                                    </div>
                                                @endif
                                            </div>
                                            <input id="thumbnail" name="thumbnail" type="file" class="d-none" accept="image/*" />
                                            <button type="button" class="btn btn-primary btn-block thumbnail-upload-btn" id="thumbnail-upload-btn">
                                                <i class="fas fa-upload"></i> Upload Thumbnail
                                            </button>
                                        </div>
                                        @error('thumbnail')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                    </div>
                                </div>

                                {{-- Gallery Upload --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">
                                            <i class="fas fa-images text-success"></i> Product Gallery
                                        </label>
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-info-circle"></i> Drag & drop or click to upload (Max 10 images)
                                        </p>
                                        
                                        {{-- Drop Zone --}}
                                        <div class="gallery-drop-zone" id="gallery-drop-zone">
                                            <div class="drop-zone-content">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                                                <p class="mt-2 mb-1"><strong>Drag & Drop</strong> images here</p>
                                                <p class="text-muted small mb-0">or click to browse</p>
                                                <span class="badge badge-info mt-2">Supports: JPG, PNG, WEBP</span>
                                            </div>
                                            <input id="gallery-input" name="images[]" type="file" class="d-none" accept="image/*" multiple />
                                        </div>

                                        {{-- New Images Preview --}}
                                        <div class="new-gallery-preview mt-3" id="new-gallery-preview"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Existing Gallery Images --}}
                            @if(!empty($product) && $product->images->isNotEmpty())
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <h5 class="mb-3">
                                            <i class="fas fa-photo-video text-info"></i> Current Gallery Images
                                            <span class="badge badge-secondary ml-2">{{ $product->images->count() }} images</span>
                                        </h5>
                                        <div class="existing-gallery-container" id="existing-gallery-container">
                                            @foreach($product->images as $index => $galleryImage)
                                                <div class="gallery-item" data-image-id="{{ $galleryImage->id }}">
                                                    <img src="{{ asset('storage/' . $galleryImage->image) }}" alt="Gallery Image {{ $index + 1 }}" />
                                                    <div class="gallery-item-overlay">
                                                        <div class="gallery-item-actions">
                                                            <button type="button" class="btn btn-sm btn-success keep-btn" title="Keep this image" data-image-id="{{ $galleryImage->id }}">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger delete-btn" title="Delete permanently" data-image-id="{{ $galleryImage->id }}" data-image-path="{{ $galleryImage->image }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                        <span class="gallery-item-index">{{ $index + 1 }}</span>
                                                    </div>
                                                    <input type="hidden" name="existing_images[]" value="{{ $galleryImage->id }}" class="keep-checkbox" data-image-id="{{ $galleryImage->id }}" />
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-light">
                            <small class="text-muted">
                                <i class="fas fa-lightbulb text-warning"></i> 
                                Tip: Upload high-quality images for better customer experience. First image will be used as main product image.
                            </small>
                        </div>
                    </div>
                    </div> {{-- /section-images --}}

                    {{-- ===== TAB: Description & SEO ===== --}}
                    <div id="section-seo">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="short_description">Short Description</label>
                                <textarea id="short_description" name="short_description" rows="2" class="form-control" placeholder="Short description">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                                @error('short_description')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="2" class="form-control" placeholder="Full description">{{ old('description', $product->description ?? '') }}</textarea>
                                @error('description')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="meta_title">Meta Title</label>
                                <input id="meta_title" name="meta_title" type="text" value="{{ old('meta_title', $product->meta_title ?? '') }}" class="form-control" placeholder="SEO Title" />
                                @error('meta_title')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="meta_keywords">Meta Keywords</label>
                                <input id="meta_keywords" name="meta_keywords" type="text" value="{{ old('meta_keywords', $product->meta_keywords ?? '') }}" class="form-control" placeholder="keyword1, keyword2" />
                                @error('meta_keywords')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="meta_description">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" rows="2" class="form-control" placeholder="SEO description">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
                        @error('meta_description')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">{{ empty($product) ? 'Create' : 'Update' }}</button>
                        <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                    </div> {{-- /section-seo --}}
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
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
/* Section spacing */
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
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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

.gallery-item-actions .btn {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.gallery-item-actions .btn:hover {
    transform: scale(1.1);
}

.gallery-item-index {
    position: absolute;
    top: 8px;
    left: 8px;
    width: 24px;
    height: 24px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: bold;
}

.gallery-item.keep .gallery-item-overlay {
    background: rgba(40, 167, 69, 0.8);
}

/* Progress Bar for Upload */
.upload-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: #e9ecef;
}

.upload-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #28a745, #20c997);
    width: 0%;
    transition: width 0.3s ease;
}

/* File Size Badge */
.file-size-badge {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
}

/* Responsive */
@media (max-width: 768px) {
    .thumbnail-preview-box {
        height: 150px;
    }
    
    .gallery-drop-zone {
        padding: 20px 15px;
    }
    
    .gallery-item {
        width: 100px;
        height: 100px;
    }
    
    .new-gallery-item {
        width: 80px;
        height: 80px;
    }
}

/* Variant Stock Status Badges */
.variant-stock-wrap { display: flex; align-items: center; gap: 6px; }
.variant-stock-wrap input { width: 70px; }
.stock-badge { display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 10px; white-space: nowrap; text-transform: uppercase; letter-spacing: 0.3px; }
.stock-badge-instock { background: #d4edda; color: #155724; }
.stock-badge-low { background: #fff3cd; color: #856404; }
.stock-badge-out { background: #f8d7da; color: #721c24; }
.stock-badge-unknown { background: #e2e3e5; color: #383d41; }

/* Variant Image Upload Styles */
.variant-image-cell {
    min-width: 180px;
}

.variant-image-upload-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.variant-image-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    max-width: 150px;
    min-height: 60px;
    background: #f8f9fa;
    border: 1px dashed #dee2e6;
    border-radius: 6px;
    padding: 5px;
    align-items: center;
    justify-content: center;
}

.variant-image-preview.has-images {
    border-style: solid;
    border-color: #28a745;
    background: #f0fff4;
}

.variant-preview-img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.variant-preview-img-wrapper {
    position: relative;
}

.variant-preview-img-wrapper .remove-variant-img {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 18px;
    height: 18px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.variant-preview-img-wrapper .remove-variant-img:hover {
    background: #c82333;
}

.upload-hint {
    font-size: 10px !important;
}

/* Bulk Update Toolbar Styles */
.bulk-update-bar {
    background: #f0f7ff;
    border: 1px solid #b8daff;
    border-radius: 6px;
    margin-bottom: 10px;
    overflow: hidden;
}

.bulk-update-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    cursor: pointer;
    user-select: none;
    transition: background 0.2s ease;
}

.bulk-update-header:hover {
    background: #e3efff;
}

.bulk-update-header .bulk-chevron {
    transition: transform 0.3s ease;
    font-size: 12px;
    color: #6c757d;
}

.bulk-update-header .bulk-chevron.open {
    transform: rotate(180deg);
}

.bulk-update-body {
    padding: 12px 14px 14px;
    border-top: 1px solid #b8daff;
    background: #fff;
}

.bulk-update-body .form-group label {
    font-weight: 600;
}

#bulk-apply-btn {
    font-weight: 600;
    letter-spacing: 0.3px;
    transition: all 0.2s ease;
}

#bulk-apply-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(23, 162, 184, 0.3);
}

/* Flash highlight on updated variant rows */
@keyframes row-flash {
    0% { background-color: #fff3cd; }
    100% { background-color: transparent; }
}

.variant-row-updated {
    animation: row-flash 0.8s ease;
}

/* Variant Summary Bar */
.variant-summary-bar {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 10px 14px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 6px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.variant-summary-bar .summary-stat {
    display: flex;
    align-items: center;
    gap: 6px;
}

.variant-summary-bar .summary-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: #fff;
}

.variant-summary-bar .summary-icon-total { background: #6c757d; }
.variant-summary-bar .summary-icon-stock { background: #007bff; }

.variant-summary-bar .summary-value {
    font-size: 18px;
    font-weight: 700;
    color: #212529;
    line-height: 1;
}

.variant-summary-bar .summary-label {
    font-size: 11px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    font-weight: 600;
}

.variant-summary-bar .summary-badge {
    font-size: 14px;
    font-weight: 700;
    padding: 2px 10px;
    border-radius: 10px;
    line-height: 1.4;
}

.variant-summary-bar .summary-badge-instock { background: #d4edda; color: #155724; }
.variant-summary-bar .summary-badge-low { background: #fff3cd; color: #856404; }
.variant-summary-bar .summary-badge-out { background: #f8d7da; color: #721c24; }

.variant-summary-bar .summary-divider {
    width: 1px;
    height: 30px;
    background: #ced4da;
}

.export-csv-btn {
    margin-left: auto;
    font-weight: 600;
    font-size: 12px;
    padding: 4px 12px;
    white-space: nowrap;
    transition: all 0.2s ease;
    border-radius: 4px;
}

.export-csv-btn:hover {
    background: #6c757d;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(108, 117, 125, 0.3);
}

/* Attribute Swatches & Pills */
.attr-group {
    margin-bottom: 16px;
    padding: 10px 12px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    transition: border-color 0.2s ease;
}
.attr-group:focus-within {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.12);
}
.attr-group-label {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 13px;
}
.attr-group-label i {
    color: #6c757d;
    font-size: 14px;
}
.attr-options {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

/* Color Swatch */
.attr-swatch {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 3px solid #dee2e6;
    cursor: pointer;
    position: relative;
    transition: all 0.15s ease;
    padding: 0;
    outline: none;
    display: flex;
    align-items: center;
    justify-content: center;
}
.attr-swatch:hover {
    transform: scale(1.15);
    border-color: #adb5bd;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.attr-swatch.selected {
    border-color: #007bff;
    transform: scale(1.1);
    box-shadow: 0 0 0 3px rgba(0,123,255,0.3);
}
.attr-swatch.selected::after {
    content: '\f00c';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    font-size: 12px;
    color: #fff;
    text-shadow: 0 1px 3px rgba(0,0,0,0.5);
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.attr-swatch .swatch-label {
    position: absolute;
    bottom: -18px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 9px;
    white-space: nowrap;
    color: #6c757d;
    opacity: 0;
    transition: opacity 0.2s ease;
    pointer-events: none;
}
.attr-swatch:hover .swatch-label {
    opacity: 1;
}
/* Light swatches (white, yellow, etc.) need a visible check */
.attr-swatch.selected.light-swatch::after {
    color: #212529;
    text-shadow: none;
}

/* Pill Button */
.attr-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 14px;
    border: 1.5px solid #ced4da;
    border-radius: 20px;
    background: #fff;
    color: #495057;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    outline: none;
}
.attr-pill:hover {
    border-color: #007bff;
    color: #007bff;
    background: #f0f7ff;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,123,255,0.15);
}
.attr-pill.selected {
    border-color: #007bff;
    background: #007bff;
    color: #fff;
    box-shadow: 0 2px 6px rgba(0,123,255,0.3);
}
.attr-pill.selected:hover {
    background: #0069d9;
    border-color: #0062cc;
}
.attr-pill.selected i {
    display: inline-block;
}
.attr-pill i {
    display: none;
    font-size: 10px;
    margin-left: 2px;
}

/* Combination Preview Styles */
.combination-preview {
    margin-top: 12px;
    border: 1px solid #c3e6cb;
    border-radius: 6px;
    background: #f0fff4;
    overflow: hidden;
}

.combination-preview .preview-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #d4edda;
    border-bottom: 1px solid #c3e6cb;
    font-size: 13px;
}

.combination-preview .preview-toggle-all {
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    user-select: none;
}

.combination-preview .preview-body {
    max-height: 220px;
    overflow-y: auto;
    padding: 6px 10px;
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}

.combination-preview .combo-checkbox-label {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 16px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
}

.combination-preview .combo-checkbox-label:hover {
    border-color: #28a745;
    background: #f0fff4;
}

.combination-preview .combo-checkbox-label input[type="checkbox"] {
    margin: 0;
    cursor: pointer;
}

.combination-preview .combo-checkbox-label.checked {
    border-color: #28a745;
    background: #d4edda;
}

.combination-preview .combo-checkbox-label.unchecked {
    opacity: 0.6;
}

.combination-preview .preview-empty {
    padding: 16px;
    text-align: center;
    color: #6c757d;
    font-size: 13px;
    width: 100%;
}

/* Unsaved Changes Badge */
.unsaved-badge {
    display: none;
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffc107;
    border-radius: 6px;
    padding: 10px 16px;
    margin-bottom: 14px;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(255, 193, 7, 0.2);
    animation: unsavedSlideDown 0.3s ease;
    position: relative;
}
.unsaved-badge i {
    margin-right: 6px;
}
.unsaved-dismiss {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #856404;
    font-size: 20px;
    line-height: 1;
    cursor: pointer;
    padding: 0 4px;
    opacity: 0.6;
    transition: opacity 0.15s;
}
.unsaved-dismiss:hover {
    opacity: 1;
}
@keyframes unsavedSlideDown {
    from { opacity: 0; transform: translateY(-12px); }
    to { opacity: 1; transform: translateY(0); }
}

/* AJAX Validation Error Styles */
.ajax-validation-alert {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.is-invalid-ajax {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.ajax-field-error {
    display: block;
    margin-top: 4px;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/variant-image-manager.js') }}"></script>
<script src="{{ asset('assets/js/variant-export.js') }}"></script>
@endpush

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== THUMBNAIL UPLOAD ==========
    const thumbnailInput = document.getElementById('thumbnail');
    const thumbnailUploadBtn = document.getElementById('thumbnail-upload-btn');
    const thumbnailPreview = document.getElementById('thumbnail-preview');
    const thumbnailPreviewBox = document.getElementById('thumbnail-preview-box');
    const thumbnailRemoveBtn = document.getElementById('thumbnail-remove-btn');

    if (thumbnailUploadBtn) {
        thumbnailUploadBtn.addEventListener('click', function() {
            thumbnailInput.click();
        });
    }

    if (thumbnailInput) {
        thumbnailInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    return;
                }

                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size should not exceed 5MB.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    // Remove placeholder if exists
                    const placeholder = thumbnailPreviewBox.querySelector('.thumbnail-placeholder');
                    if (placeholder) {
                        placeholder.remove();
                    }

                    // Create or update preview image
                    if (thumbnailPreview) {
                        thumbnailPreview.src = e.target.result;
                    } else {
                        const img = document.createElement('img');
                        img.id = 'thumbnail-preview';
                        img.src = e.target.result;
                        img.alt = 'Thumbnail Preview';
                        thumbnailPreviewBox.insertBefore(img, thumbnailPreviewBox.firstChild);
                    }

                    // Add overlay with remove button
                    if (!thumbnailPreviewBox.querySelector('.thumbnail-overlay')) {
                        const overlay = document.createElement('div');
                        overlay.className = 'thumbnail-overlay';
                        overlay.id = 'thumbnail-overlay';
                        overlay.innerHTML = `
                            <button type="button" class="btn-remove-thumbnail" id="thumbnail-remove-btn" title="Remove">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                        thumbnailPreviewBox.appendChild(overlay);

                        // Add remove functionality
                        overlay.querySelector('#thumbnail-remove-btn').addEventListener('click', removeThumbnail);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function removeThumbnail() {
        if (confirm('Are you sure you want to remove the thumbnail?')) {
            // Clear the input
            thumbnailInput.value = '';

            // Remove preview image
            const img = thumbnailPreviewBox.querySelector('img');
            if (img) img.remove();

            // Remove overlay
            const overlay = thumbnailPreviewBox.querySelector('.thumbnail-overlay');
            if (overlay) overlay.remove();

            // Add placeholder back
            const placeholder = document.createElement('div');
            placeholder.className = 'thumbnail-placeholder';
            placeholder.innerHTML = `
                <i class="fas fa-image fa-3x text-muted"></i>
                <p class="mt-2 mb-0 text-muted">No thumbnail selected</p>
            `;
            thumbnailPreviewBox.appendChild(placeholder);
        }
    }

    if (thumbnailRemoveBtn) {
        thumbnailRemoveBtn.addEventListener('click', removeThumbnail);
    }

    // ========== GALLERY DROP ZONE ==========
    const galleryDropZone = document.getElementById('gallery-drop-zone');
    const galleryInput = document.getElementById('gallery-input');
    const newGalleryPreview = document.getElementById('new-gallery-preview');
    const maxGalleryImages = 10;
    let newGalleryFiles = [];

    if (galleryDropZone) {
        // Click to upload
        galleryDropZone.addEventListener('click', function() {
            galleryInput.click();
        });

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            galleryDropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop zone when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            galleryDropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            galleryDropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            galleryDropZone.classList.add('drag-over');
        }

        function unhighlight(e) {
            galleryDropZone.classList.remove('drag-over');
        }

        // Handle dropped files
        galleryDropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        // Handle selected files
        galleryInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            const validFiles = Array.from(files).filter(file => {
                if (!file.type.startsWith('image/')) {
                    alert(`${file.name} is not an image. Please select only image files.`);
                    return false;
                }
                if (file.size > 5 * 1024 * 1024) {
                    alert(`${file.name} exceeds 5MB size limit.`);
                    return false;
                }
                return true;
            });

            if (newGalleryFiles.length + validFiles.length > maxGalleryImages) {
                alert(`You can only upload up to ${maxGalleryImages} images. Current: ${newGalleryFiles.length}, Attempting to add: ${validFiles.length}`);
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
                item.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}">
                    <button type="button" class="remove-new-btn" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                item.querySelector('.remove-new-btn').addEventListener('click', function() {
                    item.remove();
                    newGalleryFiles = newGalleryFiles.filter(f => f !== file);
                });

                newGalleryPreview.appendChild(item);
            };
            reader.readAsDataURL(file);
        }
    }

    // ========== EXISTING GALLERY IMAGES ==========
    const existingGalleryContainer = document.getElementById('existing-gallery-container');
    const deletedImageIds = []; // Track deleted image IDs

    if (existingGalleryContainer) {
        // Keep button functionality
        document.querySelectorAll('.keep-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const imageId = this.dataset.imageId;
                const galleryItem = this.closest('.gallery-item');
                const keepCheckbox = galleryItem.querySelector('.keep-checkbox');

                if (keepCheckbox) {
                    // Toggle keep status
                    if (galleryItem.classList.contains('keep')) {
                        galleryItem.classList.remove('keep');
                        keepCheckbox.disabled = true;
                        // Add to deleted images
                        if (!deletedImageIds.includes(imageId)) {
                            deletedImageIds.push(imageId);
                        }
                    } else {
                        galleryItem.classList.add('keep');
                        keepCheckbox.disabled = false;
                        // Remove from deleted images
                        const index = deletedImageIds.indexOf(imageId);
                        if (index > -1) {
                            deletedImageIds.splice(index, 1);
                        }
                    }
                }
            });
        });

        // Delete button functionality
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const imageId = this.dataset.imageId;
                const imagePath = this.dataset.imagePath;
                const galleryItem = this.closest('.gallery-item');

                if (confirm('Are you sure you want to permanently delete this image?')) {
                    // Remove from DOM
                    galleryItem.remove();

                    // Track deleted image
                    if (!deletedImageIds.includes(imageId)) {
                        deletedImageIds.push(imageId);
                    }
                }
            });
        });
    }

    // Add hidden input for deleted images
    if (existingGalleryContainer && deletedImageIds.length > 0) {
        const form = existingGalleryContainer.closest('form');
        if (form) {
            const deletedInput = document.createElement('input');
            deletedInput.type = 'hidden';
            deletedInput.name = 'deleted_images[]';
            deletedInput.id = 'deleted-images-input';
            form.appendChild(deletedInput);
        }
    }

    // ========== DRAG AND REORDER GALLERY IMAGES ==========
    let draggedItem = null;

    if (existingGalleryContainer) {
        existingGalleryContainer.addEventListener('dragstart', function(e) {
            if (e.target.classList.contains('gallery-item')) {
                draggedItem = e.target;
                e.target.style.opacity = '0.4';
            }
        });

        existingGalleryContainer.addEventListener('dragend', function(e) {
            if (e.target.classList.contains('gallery-item')) {
                e.target.style.opacity = '1';
                draggedItem = null;
                updateImageOrder();
            }
        });

        existingGalleryContainer.addEventListener('dragover', function(e) {
            e.preventDefault();
            const afterElement = getDragAfterElement(existingGalleryContainer, e.clientX);
            if (draggedItem && afterElement) {
                existingGalleryContainer.insertBefore(draggedItem, afterElement);
            } else if (draggedItem) {
                existingGalleryContainer.appendChild(draggedItem);
            }
        });
    }

    function getDragAfterElement(container, x) {
        const draggableElements = [...container.querySelectorAll('.gallery-item:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = x - box.left - box.width / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    function updateImageOrder() {
        // Update the order in hidden inputs or data attributes
        const items = existingGalleryContainer.querySelectorAll('.gallery-item');
        items.forEach((item, index) => {
            item.dataset.order = index + 1;
            const imgIndex = item.querySelector('.gallery-item-index');
            if (imgIndex) {
                imgIndex.textContent = index + 1;
            }
        });
    }

    // ========== AJAX FORM SUBMISSION ==========
    // Helper: display validation errors inline without page reload
    function displayAjaxErrors(errors) {
        // Remove any existing AJAX error alerts
        var existingAlert = document.querySelector('.ajax-validation-alert');
        if (existingAlert) existingAlert.remove();

        // Remove previous inline errors
        document.querySelectorAll('.ajax-field-error').forEach(function(el) { el.remove(); });
        document.querySelectorAll('.is-invalid-ajax').forEach(function(el) { el.classList.remove('is-invalid-ajax'); });

        // Count total errors
        var totalErrors = 0;
        Object.keys(errors).forEach(function(field) {
            totalErrors += errors[field].length;
        });
        if (totalErrors === 0) return;

        // Show error alert at the top of the form
        var form = document.querySelector('form[method="POST"]');
        if (!form) return;
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible ajax-validation-alert';
        alertDiv.innerHTML = '<button type="button" class="close" data-dismiss="alert">&times;</button>'
            + '<h5><i class="icon fas fa-ban"></i> Validation Error!</h5>'
            + '<p>Please fix the following errors and try again.</p>'
            + '<ul>';
        Object.keys(errors).forEach(function(field) {
            errors[field].forEach(function(msg) {
                alertDiv.innerHTML += '<li>' + msg + '</li>';
            });
        });
        alertDiv.innerHTML += '</ul>';
        form.insertBefore(alertDiv, form.firstChild);

        // Highlight fields with errors (simple approach: find by name attribute)
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

    // Route URLs for UnsavedChanges.ignore()
    var routeIndex = '{{ route('admin.product.index') }}';

    const productForm = document.querySelector('form[method="POST"]');
    if (productForm) {
        // ========== UNSAVED CHANGES WARNING ==========
        if (typeof UnsavedChanges !== 'undefined') {
            UnsavedChanges.watch(productForm);
            // Ignore Back/Cancel links that go to the product index
            UnsavedChanges.ignore(routeIndex);
        }

        productForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // DON'T call markClean here! We mark clean only AFTER a successful AJAX response.
            // If AJAX fails, UnsavedChanges.markDirty() will be called in the error handler.

            // Update deleted images hidden input
            var deletedInput = document.getElementById('deleted-images-input');
            if (deletedInput) {
                deletedInput.value = JSON.stringify(deletedImageIds);
            }

            // Re-enable all variant inputs so FormData captures everything
            if (variantsTableBody) {
                variantsTableBody.querySelectorAll('input').forEach(function(inp) {
                    inp.disabled = false;
                });
            }

            // Build FormData from the form (captures all standard + file inputs automatically)
            var formData = new FormData(productForm);

            // Note: VariantImageManager file inputs are already inside the form,
            // so FormData captures them automatically. No manual append needed.
            // VariantImageManager.collectFiles() is available for other use cases.

            // 🔍 DEBUG: dump variant-related FormData entries to browser console
            console.group('🔍 FormData — Variant Images Debug');
            var debugKeys = ['variant_existing_images', 'variant_images', 'variants'];
            for (var pair of formData.entries()) {
                for (var dk = 0; dk < debugKeys.length; dk++) {
                    if (pair[0].indexOf(debugKeys[dk]) === 0) {
                        var val = pair[1];
                        if (val instanceof File) {
                            console.log(pair[0] + ' → [File] ' + val.name + ' (' + (val.size / 1024).toFixed(1) + ' KB, type: ' + val.type + ')');
                        } else {
                            console.log(pair[0] + ' → ' + val);
                        }
                        break;
                    }
                }
            }
            console.groupEnd();

            // Show loading state
            var submitBtn = productForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            }

            // Remove any previous AJAX error indicators
            var prevAlert = productForm.querySelector('.ajax-validation-alert');
            if (prevAlert) prevAlert.remove();
            productForm.querySelectorAll('.ajax-field-error').forEach(function(el) { el.remove(); });
            productForm.querySelectorAll('.is-invalid-ajax').forEach(function(el) { el.classList.remove('is-invalid-ajax'); });

            // Determine the HTTP method (supports PUT via _method)
            var method = productForm.querySelector('input[name="_method"]');
            var httpMethod = method ? method.value : 'POST';

            $.ajax({
                url: productForm.action,
                method: httpMethod,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (typeof Toast !== 'undefined') {
                        Toast.success(response.message || 'Saved successfully!');
                    }

                    // Mark form as clean — no unsaved changes warning
                    if (typeof UnsavedChanges !== 'undefined') {
                        UnsavedChanges.markClean();
                    }

                    // Re-enable submit button
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = productForm.querySelector('input[name="_method"]')
                            ? 'Update'
                            : 'Create';
                    }

                    // If there's a redirect, do it after a short delay so user sees the toast
                    if (response.redirect) {
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1200);
                    }
                },
                error: function(xhr) {
                    // Re-enable submit button
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = productForm.querySelector('input[name="_method"]')
                            ? 'Update'
                            : 'Create';
                    }

                    if (xhr.status === 422) {
                        // Re-mark form as dirty since saving failed
                    if (typeof UnsavedChanges !== 'undefined') {
                        UnsavedChanges.markDirty();
                    }

                    // Validation errors — display inline, preserve all variant data
                        var errors = xhr.responseJSON ? xhr.responseJSON.errors : {};
                        displayAjaxErrors(errors);

                        // Also show a toast
                        if (typeof Toast !== 'undefined') {
                            Toast.error('Please fix the validation errors and try again.');
                        }

                        // Scroll to the error alert
                        var alertEl = productForm.querySelector('.ajax-validation-alert');
                        if (alertEl) {
                            alertEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    } else if (xhr.status === 500) {
                        // Server error — show toast
                        var errorMsg = 'A server error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        if (typeof Toast !== 'undefined') {
                            Toast.error(errorMsg, 'Server Error', 8000);
                        } else {
                            alert(errorMsg);
                        }
                    } else if (xhr.status === 401 || xhr.status === 419) {
                        // Session expired or CSRF token mismatch — redirect to login
                        var loginUrl = '{{ route('login') }}';
                        if (typeof Toast !== 'undefined') {
                            Toast.error('Session expired. Redirecting to login...', 'Session Expired', 3000);
                        }
                        setTimeout(function() {
                            window.location.href = loginUrl;
                        }, 2000);
                    } else {
                        // Other error
                        var errMsg = 'An error occurred (HTTP ' + xhr.status + '). Please try again.';
                        if (typeof Toast !== 'undefined') {
                            Toast.error(errMsg, 'Error', 8000);
                        } else {
                            alert(errMsg);
                        }
                    }
                }
            });
        });
    }

    // Add hidden input for deleted gallery images (if not already present)
    if (document.querySelector('#existing-gallery-container') && !document.getElementById('deleted-images-input')) {
        if (productForm) {
            var delInput = document.createElement('input');
            delInput.type = 'hidden';
            delInput.name = 'deleted_images[]';
            delInput.id = 'deleted-images-input';
            productForm.appendChild(delInput);
        }
    }

    // ========== PRODUCT VARIANTS (Existing Functionality) ==========
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const productTypeSelect = document.getElementById('product_type');
    const variableFields = document.getElementById('variable-fields');
    const generateVariantsBtn = document.getElementById('generate-variants');
    const variantsContainer = document.getElementById('variants-container');
    const variantsTableBody = document.getElementById('variants-table-body');
    const attributes = @json($attributes ?? []);
    const existingVariants = @json(isset($product) ? ($product->variants ?? []) : []);

    // ─── Attribute toggle helpers ───

    /** Read all currently-selected attribute values from the swatch/pill UI */
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

    /** Update each group's "X selected" count label */
    function updateSelectedCounts() {
        document.querySelectorAll('.attr-group').forEach(function(group) {
            var sel = group.querySelectorAll('.attr-option.selected').length;
            var label = group.querySelector('.attr-selected-count');
            if (label) label.textContent = sel + ' selected';
        });
    }

    /** Toggle a single attribute option on/off */
    function toggleAttributeOption(btn) {
        btn.classList.toggle('selected');
        // Light color detection for swatches
        if (btn.classList.contains('attr-swatch')) {
            var bg = btn.style.backgroundColor || '';
            var rgb = parseRgb(bg);
            if (rgb) {
                var brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
                btn.classList.toggle('light-swatch', brightness > 180);
            }
        }
        updateSelectedCounts();
        updateCombinationPreview();
        updateVisibleColumns();
        // Reset variants table when selection changes
        if (variantsContainer) variantsContainer.style.display = 'none';
        if (variantsTableBody) variantsTableBody.innerHTML = '';
    }

    function parseRgb(str) {
        if (!str) return null;
        // Handle rgb/rgba format
        var m = str.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
        if (m) return { r: parseInt(m[1]), g: parseInt(m[2]), b: parseInt(m[3]) };
        // Handle hex colors: #RRGGBB or #RGB
        var hex = str.match(/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})/i);
        if (hex) return { r: parseInt(hex[1], 16), g: parseInt(hex[2], 16), b: parseInt(hex[3], 16) };
        var hexShort = str.replace('#','').match(/^([a-f\d])([a-f\d])([a-f\d])$/i);
        if (hexShort) return {
            r: parseInt(hexShort[1] + hexShort[1], 16),
            g: parseInt(hexShort[2] + hexShort[2], 16),
            b: parseInt(hexShort[3] + hexShort[3], 16)
        };
        // Handle named CSS colors (most common for attribute values)
        var named = {
            white: { r:255,g:255,b:255 }, snow: { r:255,g:250,b:250 }, ivory: { r:255,g:255,b:240 },
            lightyellow: { r:255,g:255,b:224 }, lightgoldenrodyellow: { r:250,g:250,b:210 },
            beige: { r:245,g:245,b:220 }, cornsilk: { r:255,g:248,b:220 },
            lemonchiffon: { r:255,g:250,b:205 }, oldlace: { r:253,g:245,b:230 },
            floralwhite: { r:255,g:250,b:240 }, lavender: { r:230,g:230,b:250 },
            lightcyan: { r:224,g:255,b:255 }, aliceblue: { r:240,g:248,b:255 },
            ghostwhite: { r:248,g:248,b:255 }, honeydew: { r:240,g:255,b:240 },
            mintcream: { r:245,g:255,b:250 }, azure: { r:240,g:255,b:255 },
            seashell: { r:255,g:245,b:238 }, papayawhip: { r:255,g:239,b:213 },
            blanchedalmond: { r:255,g:235,b:205 }, mistyrose: { r:255,g:228,b:225 },
            bisque: { r:255,g:228,b:196 }, moccasin: { r:255,g:228,b:181 },
            navajowhite: { r:255,g:222,b:173 }, peachpuff: { r:255,g:218,b:185 },
            yellow: { r:255,g:255,b:0 }, gold: { r:255,g:215,b:0 },
            pink: { r:255,g:192,b:203 }, lightpink: { r:255,g:182,b:193 },
            red: { r:255,g:0,b:0 }, tomato: { r:255,g:99,b:71 },
            orange: { r:255,g:165,b:0 }, coral: { r:255,g:127,b:80 },
            green: { r:0,g:128,b:0 }, lime: { r:0,g:255,b:0 },
            blue: { r:0,g:0,b:255 }, navy: { r:0,g:0,b:128 },
            purple: { r:128,g:0,b:128 }, fuchsia: { r:255,g:0,b:255 },
            maroon: { r:128,g:0,b:0 }, brown: { r:165,g:42,b:42 },
            black: { r:0,g:0,b:0 }, gray: { r:128,g:128,b:128 },
            silver: { r:192,g:192,b:192 }, teal: { r:0,g:128,b:128 },
            cyan: { r:0,g:255,b:255 }, aqua: { r:0,g:255,b:255 }
        };
        var lower = str.trim().toLowerCase();
        return named[lower] || null;
    }

    // Attach click handlers to all attribute options
    document.querySelectorAll('.attr-option').forEach(function(btn) {
        btn.addEventListener('click', function() {
            toggleAttributeOption(this);
        });
    });

    // Sync swatches/pills with existing variant attributes on edit
    function syncExistingAttributes() {
        if (!existingVariants || existingVariants.length === 0) return;

        var collectedValues = {};
        existingVariants.forEach(function(variant) {
            var attrs = variant.attributes || {};
            Object.keys(attrs).forEach(function(attrName) {
                if (!collectedValues[attrName]) collectedValues[attrName] = [];
                if (collectedValues[attrName].indexOf(attrs[attrName]) === -1) {
                    collectedValues[attrName].push(attrs[attrName]);
                }
            });
        });

        document.querySelectorAll('.attr-option').forEach(function(opt) {
            var attrName = opt.getAttribute('data-attr-name');
            var val = opt.getAttribute('data-value');
            if (collectedValues[attrName] && collectedValues[attrName].indexOf(val) !== -1) {
                opt.classList.add('selected');
            }
        });
        updateSelectedCounts();
    }

    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        });
    }

    if (productTypeSelect && variableFields) {
        productTypeSelect.addEventListener('change', function() {
            if (this.value === 'variable') {
                variableFields.style.display = 'block';
                syncExistingAttributes();
            } else {
                variableFields.style.display = 'none';
                variantsContainer.style.display = 'none';
            }
        });
    }

    // ─── Attribute toggle helpers END ───

    // ========== COMBINATION PREVIEW & GENERATE ==========
    const comboPreview = document.getElementById('combination-preview');
    const previewBody = document.getElementById('preview-body');
    const previewCount = document.getElementById('preview-count');
    const previewSelectAll = document.getElementById('preview-select-all');

    function updateCombinationPreview() {
        var selectedValues = getSelectedAttributeValues();
        var combos = generateCombinations(selectedValues);
        var keys = Object.keys(selectedValues);

        if (keys.length === 0 || combos.length === 0) {
            comboPreview.style.display = 'none';
            return;
        }

        comboPreview.style.display = 'block';

        // Build combo labels
        var html = '';
        combos.forEach(function(combo, i) {
            var label = Object.values(combo).join(' - ');
            var comboJson = JSON.stringify(combo).replace(/'/g, '&#39;');
            html += '<label class="combo-checkbox-label checked">'
                  + "<input type=\"checkbox\" class=\"combo-cb\" checked data-combo='" + comboJson + "' /> "
                  + label
                  + '</label>';
        });
        previewBody.innerHTML = html;
        previewCount.textContent = combos.length + ' combinations';

        // Bind toggle-all
        if (previewSelectAll) {
            previewSelectAll.checked = true;
        }
    }

    // Preview updates are triggered by toggleAttributeOption() — no separate Select2 handlers needed

    // Toggle individual combo label class on checkbox change
    if (previewBody) {
        previewBody.addEventListener('change', function(e) {
            if (e.target.classList.contains('combo-cb')) {
                var label = e.target.closest('.combo-checkbox-label');
                if (label) {
                    label.classList.toggle('checked', e.target.checked);
                    label.classList.toggle('unchecked', !e.target.checked);
                }
                // Update Select All
                var allCbs = previewBody.querySelectorAll('.combo-cb');
                var allChecked = true;
                allCbs.forEach(function(cb) { if (!cb.checked) allChecked = false; });
                if (previewSelectAll) previewSelectAll.checked = allChecked;
            }
        });
    }

    // Select All toggle
    if (previewSelectAll && previewBody) {
        previewSelectAll.addEventListener('change', function() {
            var checked = this.checked;
            previewBody.querySelectorAll('.combo-cb').forEach(function(cb) {
                cb.checked = checked;
                var label = cb.closest('.combo-checkbox-label');
                if (label) {
                    label.classList.toggle('checked', checked);
                    label.classList.toggle('unchecked', !checked);
                }
            });
        });
    }

    if (generateVariantsBtn) {
        generateVariantsBtn.addEventListener('click', function() {
            // Auto-set product_type to 'variable' when variants are generated
            if (productTypeSelect) {
                productTypeSelect.value = 'variable';
                // Trigger change event so variable-fields section shows
                if (typeof jQuery !== 'undefined') {
                    $(productTypeSelect).trigger('change');
                }
            }
            // If preview is visible, use checked combos from preview
            if (comboPreview.style.display !== 'none' && previewBody) {
                var filtered = [];
                previewBody.querySelectorAll('.combo-cb:checked').forEach(function(cb) {
                    var raw = cb.getAttribute('data-combo');
                    if (raw) {
                        try {
                            var combo = JSON.parse(raw.replace(/&#39;/g, "'"));
                            filtered.push(combo);
                        } catch(e) {}
                    }
                });
                renderVariants(filtered);
                setTimeout(updateVisibleColumns, 50);
            } else {
                // Fallback: generate all combinations from selected swatches/pills
                var selectedValues = getSelectedAttributeValues();
                var combinations = generateCombinations(selectedValues);
                renderVariants(combinations);
                setTimeout(updateVisibleColumns, 50);
            }
        });
    }

    // ========== STOCK STATUS HELPERS ==========
    // Uses the product's minimum_stock field as low-stock threshold
    function getEffectiveMinStock() {
        var el = document.getElementById('minimum_stock');
        var val = el ? parseInt(el.value, 10) : NaN;
        if (val === 0) return 0;          // explicit "no minimum"
        return (!isNaN(val) && val > 0) ? val : 5;  // use set value or fallback to 5
    }

    function getStockInfo(stock) {
        var s = parseInt(stock, 10);
        if (isNaN(s) || s <= 0) return { cls: 'stock-badge-out', label: 'Out' };
        if (s <= getEffectiveMinStock()) return { cls: 'stock-badge-low', label: 'Low' };
        return { cls: 'stock-badge-instock', label: 'In Stock' };
    }

    function updateStockBadge(input) {
        var wrap = input.closest('.variant-stock-wrap');
        if (!wrap) return;
        var badge = wrap.querySelector('.stock-badge');
        if (!badge) return;
        var info = getStockInfo(input.value);
        badge.className = 'stock-badge ' + info.cls;
        badge.textContent = info.label;
    }

    // ========== VARIANT SUMMARY ==========
    const summaryBar = document.getElementById('variant-summary-bar');
    const summaryTotal = document.getElementById('summary-total');
    const summaryTotalStock = document.getElementById('summary-total-stock');
    const summaryInStock = document.getElementById('summary-instock-count');
    const summaryLow = document.getElementById('summary-low-count');
    const summaryOut = document.getElementById('summary-out-count');

    function updateVariantSummary() {
        if (!summaryBar || !variantsTableBody) return;

        var rows = variantsTableBody.querySelectorAll('tr');
        if (rows.length === 0) {
            summaryBar.style.display = 'none';
            return;
        }

        summaryBar.style.display = 'flex';

        var totalStock = 0;
        var inStockCount = 0;
        var lowCount = 0;
        var outCount = 0;

        rows.forEach(function(row) {
            var stockInput = row.querySelector('.variant-stock-input');
            if (!stockInput) return;
            var val = parseInt(stockInput.value, 10);
            if (isNaN(val)) val = 0;

            totalStock += val;

            if (val <= 0) {
                outCount++;
            } else if (val <= getEffectiveMinStock()) {
                lowCount++;
            } else {
                inStockCount++;
            }
        });

        summaryTotal.textContent = rows.length;
        summaryTotalStock.textContent = totalStock;
        summaryInStock.textContent = inStockCount;
        summaryLow.textContent = lowCount;
        summaryOut.textContent = outCount;
    }

    // ========== DYNAMIC COLUMN VISIBILITY ==========
    function updateVisibleColumns() {
        // Determine which attributes have selected values
        var activeAttrs = getSelectedAttributeValues();
        // Convert to boolean map
        Object.keys(activeAttrs).forEach(function(k) { activeAttrs[k] = true; });

        // Show/hide header columns
        var headerThs = document.querySelectorAll('#variants-table thead th[data-attr-name]');
        headerThs.forEach(function(th) {
            var attrName = th.getAttribute('data-attr-name');
            var hasValues = !!activeAttrs[attrName];
            th.style.display = hasValues ? '' : 'none';
        });

        // Show/hide data cell columns in each row
        variantsTableBody.querySelectorAll('tr').forEach(function(row) {
            var attrTds = row.querySelectorAll('td[data-attr-name]');
            attrTds.forEach(function(td) {
                var attrName = td.getAttribute('data-attr-name');
                td.style.display = !!activeAttrs[attrName] ? '' : 'none';
            });
        });
    }

    function generateCombinations(selectedValues) {
        const keys = Object.keys(selectedValues);
        if (keys.length === 0) {
            return [];
        }

        const result = [];
        const valueArrays = keys.map(key => selectedValues[key]);
        const total = valueArrays.reduce((acc, arr) => acc * arr.length, 1);

        for (let i = 0; i < total; i++) {
            const combo = {};
            let divisor = total;
            keys.forEach(function(key, idx) {
                divisor = divisor / valueArrays[idx].length;
                const valueIndex = Math.floor((i / divisor) % valueArrays[idx].length);
                combo[key] = valueArrays[idx][valueIndex];
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

        const existingKeys = new Set();
        variantsTableBody.querySelectorAll('tr').forEach(function(tr) {
            const attrInput = tr.querySelector('input[name$="[attributes]"]');
            if (attrInput && attrInput.value) {
                existingKeys.add(attrInput.value);
            }
        });

        if (typeof window._variantIndexCounter !== 'number') {
            window._variantIndexCounter = variantsTableBody.querySelectorAll('tr').length;
        }

        combinations.forEach(function(combo) {
            const comboKey = JSON.stringify(combo);
            if (existingKeys.has(comboKey)) return;

            const index = window._variantIndexCounter++;
            const tr = document.createElement('tr');

            Object.keys(combo).forEach(function(key) {
                const td = document.createElement('td');
                td.textContent = combo[key];
                td.setAttribute('data-attr-name', key);
                tr.appendChild(td);
            });

            // Use reusable VariantImageManager for image upload
            var imgMgr = new VariantImageManager(index, []);
            tr.appendChild(imgMgr.render());

            const skuTd = document.createElement('td');
            skuTd.innerHTML = '<input type="text" name="variants[' + index + '][sku]" class="form-control" placeholder="SKU" />';
            tr.appendChild(skuTd);

            const priceTd = document.createElement('td');
            priceTd.innerHTML = '<input type="number" step="0.01" name="variants[' + index + '][price]" class="form-control" placeholder="0.00" />';
            tr.appendChild(priceTd);

            var newStockInfo = getStockInfo(0);
            const stockTd = document.createElement('td');
            stockTd.innerHTML = '<div class="variant-stock-wrap"><input type="number" name="variants[' + index + '][stock]" class="form-control variant-stock-input" value="0" placeholder="0" /><span class="stock-badge ' + newStockInfo.cls + '">' + newStockInfo.label + '</span></div>';
            tr.appendChild(stockTd);

            // Live stock status update
            stockTd.querySelector('.variant-stock-input').addEventListener('input', function() {
                updateStockBadge(this);
            });

            // Select checkbox
            var selTd = document.createElement('td');
            selTd.style.cssText = 'width:40px;text-align:center;vertical-align:middle;';
            selTd.innerHTML = '<input type="checkbox" class="variant-select-cb" checked data-index="' + index + '" />';
            tr.appendChild(selTd);

            const actionTd = document.createElement('td');
            actionTd.innerHTML = '<button type="button" class="btn btn-danger btn-sm remove-variant">Remove</button>';
            tr.appendChild(actionTd);

            const nameInput2 = document.createElement('input');
            nameInput2.type = 'hidden';
            nameInput2.name = 'variants[' + index + '][name]';
            nameInput2.value = Object.values(combo).join(' - ');
            tr.appendChild(nameInput2);

            const attrInput = document.createElement('input');
            attrInput.type = 'hidden';
            attrInput.name = 'variants[' + index + '][attributes]';
            attrInput.value = JSON.stringify(combo);
            tr.appendChild(attrInput);

            variantsTableBody.appendChild(tr);
        });

        if (productTypeSelect && productTypeSelect.value === 'variable') {
            variableFields.style.display = 'block';
        }

        updateVariantSummary();
    }

    function renderExistingVariants() {
        if (!existingVariants || existingVariants.length === 0) return;

        variantsContainer.style.display = 'block';

        const existingIds = new Set();
        variantsTableBody.querySelectorAll('tr[data-variant-id]').forEach(function(tr) {
            existingIds.add(tr.getAttribute('data-variant-id'));
        });

        if (typeof window._variantIndexCounter !== 'number') {
            window._variantIndexCounter = variantsTableBody.querySelectorAll('tr').length;
        }

        existingVariants.forEach(function(variant) {
            const variantId = variant.id ? variant.id.toString() : '';
            if (variantId && existingIds.has(variantId)) return;

            const index = window._variantIndexCounter++;
            const tr = document.createElement('tr');
            if (variantId) tr.setAttribute('data-variant-id', variantId);
            const attrs = typeof variant.attributes === 'string'
            ? JSON.parse(variant.attributes)
            : (variant.attributes || {});
            const attrNames = Object.keys(attrs);
            attrNames.forEach(function(attrName) {
                const td = document.createElement('td');
                td.textContent = attrs[attrName];
                td.setAttribute('data-attr-name', attrName);
                tr.appendChild(td);
            });

            // Use reusable VariantImageManager for image upload with existing images
            var existingImgs = [];
            if (variant.images && variant.images.length > 0) {
                variant.images.forEach(function(vImg) {
                    existingImgs.push({ id: vImg.id, image: vImg.image });
                });
            }
            var imgMgr = new VariantImageManager(index, existingImgs, '{{ asset('storage') }}/');
            tr.appendChild(imgMgr.render());

            const skuTd = document.createElement('td');
            skuTd.innerHTML = '<input type="text" name="variants[' + index + '][sku]" class="form-control" value="' + (variant.sku || '') + '" placeholder="SKU" />';
            tr.appendChild(skuTd);

            const priceTd = document.createElement('td');
            priceTd.innerHTML = '<input type="number" step="0.01" name="variants[' + index + '][price]" class="form-control" value="' + (variant.price ?? '') + '" placeholder="0.00" />';
            tr.appendChild(priceTd);

            const stockVal = variant.stock ?? 0;
            var existingStockInfo = getStockInfo(stockVal);
            const stockTd = document.createElement('td');
            stockTd.innerHTML = '<div class="variant-stock-wrap"><input type="number" name="variants[' + index + '][stock]" class="form-control variant-stock-input" value="' + stockVal + '" placeholder="0" /><span class="stock-badge ' + existingStockInfo.cls + '">' + existingStockInfo.label + '</span></div>';
            tr.appendChild(stockTd);

            // Live stock status update
            stockTd.querySelector('.variant-stock-input').addEventListener('input', function() {
                updateStockBadge(this);
            });

            // Select checkbox
            var selTd = document.createElement('td');
            selTd.style.cssText = 'width:40px;text-align:center;vertical-align:middle;';
            selTd.innerHTML = '<input type="checkbox" class="variant-select-cb" checked data-index="' + index + '" />';
            tr.appendChild(selTd);

            const actionTd = document.createElement('td');
            actionTd.innerHTML = '<button type="button" class="btn btn-danger btn-sm remove-variant">Remove</button>';
            tr.appendChild(actionTd);

            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'variants[' + index + '][id]';
            idInput.value = variant.id;
            tr.appendChild(idInput);

            const nameInput2 = document.createElement('input');
            nameInput2.type = 'hidden';
            nameInput2.name = 'variants[' + index + '][name]';
            nameInput2.value = variant.name;
            tr.appendChild(nameInput2);

            const attrInput = document.createElement('input');
            attrInput.type = 'hidden';
            attrInput.name = 'variants[' + index + '][attributes]';
            attrInput.value = JSON.stringify(attrs);
            tr.appendChild(attrInput);

            variantsTableBody.appendChild(tr);
        });

        updateVariantSummary();
        setTimeout(updateVisibleColumns, 50);
    }

    if (variantsTableBody) {
        variantsTableBody.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-variant')) {
                e.target.closest('tr').remove();
                updateVariantSummary();
            }
        });

        // Live stock input changes → refresh summary
        variantsTableBody.addEventListener('input', function(e) {
            if (e.target.classList.contains('variant-stock-input')) {
                updateVariantSummary();
            }
        });
    }

    if (productTypeSelect && variableFields) {
        if (productTypeSelect.value === 'variable') {
            variableFields.style.display = 'block';
            syncExistingAttributes();
            window._variantIndexCounter = variantsTableBody.querySelectorAll('tr').length || 0;
            renderExistingVariants();
        }
    }

    // ========== SELECT ALL / DISABLE UNCHECKED ==========
    const selectAllCb = document.getElementById('select-all-variants');
    if (selectAllCb && variantsTableBody) {
        // Toggle all row checkboxes
        selectAllCb.addEventListener('change', function() {
            var checked = this.checked;
            variantsTableBody.querySelectorAll('.variant-select-cb').forEach(function(cb) {
                cb.checked = checked;
            });
        });

        // If any row checkbox is unchecked, uncheck the Select All
        variantsTableBody.addEventListener('change', function(e) {
            if (e.target.classList.contains('variant-select-cb')) {
                var allChecked = true;
                variantsTableBody.querySelectorAll('.variant-select-cb').forEach(function(cb) {
                    if (!cb.checked) allChecked = false;
                });
                selectAllCb.checked = allChecked;
            }
        });
    }

    // NOTE: Form submission is now handled by AJAX in the FORM SUBMISSION section above.
    // The AJAX handler re-enables all variant inputs before building FormData.

    // After existing variants are loaded, refresh UnsavedChanges snapshot
    // so dynamically added variant inputs are tracked from their initial state
    if (typeof UnsavedChanges !== 'undefined' && existingVariants && existingVariants.length > 0) {
        // Wait for renderExistingVariants and Select2 sync to complete
        setTimeout(function() {
            UnsavedChanges.refreshSnapshot();
        }, 500);
    }

    // ========== BULK UPDATE HANDLER ==========
    const bulkToggle = document.getElementById('bulk-update-toggle');
    const bulkBody = document.getElementById('bulk-update-body');
    const bulkSku = document.getElementById('bulk-sku');
    const bulkPrice = document.getElementById('bulk-price');
    const bulkStock = document.getElementById('bulk-stock');
    const bulkSkuPattern = document.getElementById('bulk-sku-pattern');
    const bulkApplyBtn = document.getElementById('bulk-apply-btn');
    const bulkChevron = document.querySelector('.bulk-chevron');

    if (bulkToggle && bulkBody) {
        bulkToggle.addEventListener('click', function() {
            var isOpen = bulkBody.style.display !== 'none';
            bulkBody.style.display = isOpen ? 'none' : 'block';
            if (bulkChevron) bulkChevron.classList.toggle('open', !isOpen);
        });
    }

    if (bulkApplyBtn && variantsTableBody) {
        bulkApplyBtn.addEventListener('click', function() {
            var skuPrefix = bulkSku ? bulkSku.value.trim() : '';
            var priceVal = bulkPrice ? bulkPrice.value.trim() : '';
            var stockVal = bulkStock ? bulkStock.value.trim() : '';
            var pattern = bulkSkuPattern ? bulkSkuPattern.value : 'prefix-attr';

            var rows = variantsTableBody.querySelectorAll('tr');
            if (rows.length === 0) {
                alert('No variants to update. Generate variants first.');
                return;
            }

            var updatedCount = 0;
            rows.forEach(function(row, idx) {
                var allInputs = row.querySelectorAll('input');
                var skuInput = null;
                var priceInput = null;
                var stockInput = null;
                var attrValue = '';

                allInputs.forEach(function(inp) {
                    var name = inp.getAttribute('name') || '';
                    if (name.match(/\[sku\]/)) skuInput = inp;
                    else if (name.match(/\[price\]/)) priceInput = inp;
                    else if (name.match(/\[stock\]/)) stockInput = inp;
                    else if (name.match(/\[attributes\]/)) {
                        try {
                            var parsed = JSON.parse(inp.value || '{}');
                            attrValue = Object.values(parsed).join('-');
                        } catch(e) {}
                    }
                });

                // Determine if this row will be updated
                var rowUpdated = false;

                // — SKU —
                if (skuPrefix && skuInput) {
                    var generatedSku = '';
                    if (pattern === 'prefix-only') {
                        generatedSku = skuPrefix + (idx + 1);
                    } else if (pattern === 'attr-prefix') {
                        generatedSku = attrValue ? (attrValue + '-' + skuPrefix) : (skuPrefix + (idx + 1));
                    } else {
                        // prefix-attr (default)
                        generatedSku = attrValue ? (skuPrefix + attrValue) : (skuPrefix + (idx + 1));
                    }
                    skuInput.value = generatedSku;
                    rowUpdated = true;
                }

                // — Price —
                if (priceVal !== '' && priceInput) {
                    priceInput.value = priceVal;
                    rowUpdated = true;
                }

                // — Stock —
                if (stockVal !== '' && stockInput) {
                    stockInput.value = stockVal;
                    // Update stock badge live
                    updateStockBadge(stockInput);
                    rowUpdated = true;
                }

                if (rowUpdated) {
                    updatedCount++;
                    // Flash highlight
                    row.classList.remove('variant-row-updated');
                    void row.offsetWidth; // reflow
                    row.classList.add('variant-row-updated');
                }
            });

            if (updatedCount > 0) {
                // Show brief success feedback on the button
                var origHtml = bulkApplyBtn.innerHTML;
                bulkApplyBtn.innerHTML = '<i class="fas fa-check-circle"></i> ' + updatedCount + ' Updated!';
                bulkApplyBtn.classList.remove('btn-info');
                bulkApplyBtn.classList.add('btn-success');
                setTimeout(function() {
                    bulkApplyBtn.innerHTML = origHtml;
                    bulkApplyBtn.classList.remove('btn-success');
                    bulkApplyBtn.classList.add('btn-info');
                }, 2000);
            }
            updateVariantSummary();
        });
    }

    // ========== INIT EXPORT ==========
    if (typeof VariantExport !== 'undefined') {
        VariantExport.init('variants-table-body');
    }

    // ========== VARIANT IMAGE UPLOAD HANDLER (DEPRECATED — now handled by VariantImageManager module) ==========
    function handleVariantImageUpload(files, previewContainer, variantIndex) {
        if (!files || files.length === 0) return;

        // Clear existing previews
        previewContainer.innerHTML = '';

        Array.from(files).forEach(file => {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert(`${file.name} is not an image.`);
                return;
            }

            // Validate file size (2MB max for variants)
            if (file.size > 2 * 1024 * 1024) {
                alert(`${file.name} exceeds 2MB size limit.`);
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const wrapper = document.createElement('div');
                wrapper.className = 'variant-preview-img-wrapper';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'variant-preview-img';
                img.alt = file.name;

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'remove-variant-img';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.title = 'Remove image';
                removeBtn.addEventListener('click', function() {
                    wrapper.remove();
                    // Check if no images left
                    if (previewContainer.children.length === 0) {
                        previewContainer.classList.remove('has-images');
                    }
                });

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                previewContainer.appendChild(wrapper);

                // Add has-images class if there are images
                previewContainer.classList.add('has-images');
            };
            reader.readAsDataURL(file);
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
            if (!activeId && sections.length > 0) {
                var last = sections[sections.length - 1];
                var lastBottom = last.el.offsetTop + last.el.offsetHeight;
                if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 100) {
                    activeId = last.link.getAttribute('href').substring(1);
                }
            }
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

        var scrollTimeout;
        window.addEventListener('scroll', function() {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(updateActiveTab, 50);
        });
        updateActiveTab();

        // Show/hide Variants tab based on product type
        var pt = document.getElementById('product_type');
        var vn = document.getElementById('variants-tab-nav');
        if (pt && vn) {
            function toggleVariantTab() {
                vn.style.display = pt.value === 'variable' ? '' : 'none';
            }
            pt.addEventListener('change', toggleVariantTab);
            toggleVariantTab();
        }

        // ========== QUICK ACTION TOOLBAR ==========
        var toolbar = document.getElementById('productActionToolbar');
        var statusSelect = document.getElementById('status');
        var isEditing = !!document.querySelector('input[name="_method"]');

        function submitWithStatus(statusVal) {
            if (statusSelect) statusSelect.value = statusVal;
            // Dispatch the submit event directly on the form.
            // We do NOT use nativeSubmitBtn.click() because:
            //   1) HTML5 validation can block the submit event from firing
            //   2) Programmatic .click() can cause native form submission in some browsers
            // A synthetic Event('submit') always reaches our AJAX handler.
            productForm.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        }

        if (toolbar) {
            // Save Draft
            var draftBtn = document.getElementById('action-save-draft');
            if (draftBtn) {
                draftBtn.addEventListener('click', function() {
                    submitWithStatus('draft', false);
                });
            }

            // Save & Publish
            var pubBtn = document.getElementById('action-save-publish');
            if (pubBtn) {
                pubBtn.addEventListener('click', function() {
                    submitWithStatus('published', false);
                });
            }

            // Create Variants (scroll to variants section, only for variable products)
            var varBtn = document.getElementById('action-create-variant');
            if (varBtn && pt) {
                pt.addEventListener('change', function() {
                    varBtn.style.display = pt.value === 'variable' ? 'inline-block' : 'none';
                });
                varBtn.addEventListener('click', function() {
                    var section = document.getElementById('section-variants');
                    if (section) {
                        // First make sure variable-fields are visible
                        var vf = document.getElementById('variable-fields');
                        if (vf && pt.value === 'variable') {
                            vf.style.display = '';
                            // Also activate the Variants tab
                            var vt = document.getElementById('variants-tab-nav');
                            if (vt) vt.style.display = '';
                            setTimeout(function() {
                                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }, 100);
                        } else if (pt.value !== 'variable') {
                            pt.value = 'variable';
                            pt.dispatchEvent(new Event('change'));
                            setTimeout(function() {
                                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }, 200);
                        }
                    }
                });
            }

            // Modify the AJAX success handler to support stay-on-page
            var origSuccess = null;
        }
    })();
});
</script>
@endsection
