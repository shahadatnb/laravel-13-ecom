@extends('admin.layouts.app')
@section('title', empty($product) ? 'Create Product' : 'Edit Product')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <h1 class="h3 m-0 font-weight-bold">
                        <i class="fas fa-box {{ empty($product) ? 'fa-plus text-success' : 'fa-edit text-primary' }}"></i>
                        {{ empty($product) ? 'Create New Product' : 'Edit Product' }}
                    </h1>
                    <div>
                        <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <form method="POST" action="{{ empty($product) ? route('admin.product.store') : route('admin.product.update', $product->id) }}" enctype="multipart/form-data" id="productForm">
        @csrf
        @if(!empty($product))
            @method('PUT')
        @endif

        <div class="row">
            {{-- Main Content --}}
            <div class="col-lg-8">
                {{-- Basic Information Card --}}
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header border-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary mr-2"></i>
                            <h3 class="card-title m-0 font-weight-bold">Basic Information</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_type">Product Type <span class="text-danger">*</span></label>
                                    <select id="product_type" name="product_type" class="form-control select2" required>
                                        <option value="">Select Type</option>
                                        <option value="simple" {{ old('product_type', $product->product_type ?? '') == 'simple' ? 'selected' : '' }}>Simple Product</option>
                                        <option value="variable" {{ old('product_type', $product->product_type ?? '') == 'variable' ? 'selected' : '' }}>Variable Product</option>
                                        <option value="digital" {{ old('product_type', $product->product_type ?? '') == 'digital' ? 'selected' : '' }}>Digital Product</option>
                                        <option value="service" {{ old('product_type', $product->product_type ?? '') == 'service' ? 'selected' : '' }}>Service</option>
                                    </select>
                                    <small class="form-text text-muted">Choose "Variable" to add variants like size, color</small>
                                    @error('product_type')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Product Status <span class="text-danger">*</span></label>
                                    <select id="status" name="status" class="form-control select2" required>
                                        <option value="draft" {{ old('status', $product->status ?? 'draft') == 'draft' ? 'selected' : '' }}>
                                            <i class="fas fa-file text-secondary"></i> Draft
                                        </option>
                                        <option value="pending" {{ old('status', $product->status ?? '') == 'pending' ? 'selected' : '' }}>
                                            <i class="fas fa-clock text-warning"></i> Pending Review
                                        </option>
                                        <option value="published" {{ old('status', $product->status ?? '') == 'published' ? 'selected' : '' }}>
                                            <i class="fas fa-check-circle text-success"></i> Published
                                        </option>
                                        <option value="hidden" {{ old('status', $product->status ?? '') == 'hidden' ? 'selected' : '' }}>
                                            <i class="fas fa-eye-slash text-info"></i> Hidden
                                        </option>
                                    </select>
                                    @error('status')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Product Name <span class="text-danger">*</span></label>
                                    <input id="name" name="name" type="text" value="{{ old('name', $product->name ?? '') }}" required class="form-control" placeholder="Enter product name" />
                                    @error('name')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name_bn">Name (Bengali)</label>
                                    <input id="name_bn" name="name_bn" type="text" value="{{ old('name_bn', $product->name_bn ?? '') }}" class="form-control" placeholder="বাংলা নাম লিখুন" />
                                    @error('name_bn')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="slug">URL Slug <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-link"></i></span>
                                        </div>
                                        <input id="slug" name="slug" type="text" value="{{ old('slug', $product->slug ?? '') }}" required class="form-control" placeholder="product-url-slug" />
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" id="generate-slug">
                                                <i class="fas fa-magic"></i> Generate
                                            </button>
                                        </div>
                                    </div>
                                    @error('slug')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sku">SKU (Stock Keeping Unit)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                        </div>
                                        <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku ?? '') }}" class="form-control" placeholder="SKU-001" />
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" id="generate-sku">
                                                <i class="fas fa-random"></i> Auto
                                            </button>
                                        </div>
                                    </div>
                                    @error('sku')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="barcode">Barcode</label>
                                    <input id="barcode" name="barcode" type="text" value="{{ old('barcode', $product->barcode ?? '') }}" class="form-control" placeholder="1234567890" />
                                    @error('barcode')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="brand_id">Brand</label>
                                    <select id="brand_id" name="brand_id" class="form-control select2">
                                        <option value="">-- Select Brand --</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('brand_id')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="category_id">Primary Category <span class="text-danger">*</span></label>
                                    <select id="category_id" name="category_id" class="form-control select2" required>
                                        <option value="">-- Select Primary Category --</option>
                                        @foreach ($categoryTree as $cat)
                                            <option value="{{ $cat['id'] }}" {{ old('category_id', $product->category_id ?? '') == $cat['id'] ? 'selected' : '' }}>
                                                {{ str_repeat('— ', $cat['depth']) }}{{ $cat['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Additional Categories <span class="text-muted small">(optional)</span></label>
                                    <div class="category-checkbox-tree" style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px; padding: 10px; background: #fff;">
                                        @php
                                            $selectedCategoryIds = old('category_ids', isset($product) ? $product->categories->pluck('id')->toArray() : []);
                                        @endphp
                                        @foreach ($categoryTree as $cat)
                                            <label class="d-flex align-items-center py-1" style="padding-left: {{ $cat['depth'] * 20 + 4 }}px;">
                                                <input type="checkbox" name="category_ids[]" value="{{ $cat['id'] }}" class="mr-2" {{ in_array($cat['id'], $selectedCategoryIds) ? 'checked' : '' }} />
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
                                    @error('category_ids')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pricing & Stock Card --}}
                <div class="card card-success card-outline shadow-sm">
                    <div class="card-header border-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-dollar-sign text-success mr-2"></i>
                            <h3 class="card-title m-0 font-weight-bold">Pricing & Inventory</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="regular_price">Regular Price (৳)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                        </div>
                                        <input id="regular_price" name="regular_price" type="number" step="0.01" value="{{ old('regular_price', $product->regular_price ?? '') }}" class="form-control" placeholder="0.00" />
                                    </div>
                                    @error('regular_price')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sale_price">Sale Price (৳)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                        </div>
                                        <input id="sale_price" name="sale_price" type="number" step="0.01" value="{{ old('sale_price', $product->sale_price ?? '') }}" class="form-control" placeholder="0.00" />
                                    </div>
                                    <small class="form-text text-muted">Leave empty for no discount</small>
                                    @error('sale_price')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cost_price">Cost Price (৳)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calculator"></i></span>
                                        </div>
                                        <input id="cost_price" name="cost_price" type="number" step="0.01" value="{{ old('cost_price', $product->cost_price ?? '') }}" class="form-control" placeholder="0.00" />
                                    </div>
                                    <small class="form-text text-muted">For profit calculation</small>
                                    @error('cost_price')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stock">Available Stock</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-boxes"></i></span>
                                        </div>
                                        <input id="stock" name="stock" type="number" value="{{ old('stock', $product->stock ?? 0) }}" class="form-control" />
                                    </div>
                                    @error('stock')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="minimum_stock">Minimum Stock Alert</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-exclamation-triangle"></i></span>
                                        </div>
                                        <input id="minimum_stock" name="minimum_stock" type="number" value="{{ old('minimum_stock', $product->minimum_stock ?? 0) }}" class="form-control" />
                                    </div>
                                    <small class="form-text text-muted">Alert when stock reaches this level</small>
                                    @error('minimum_stock')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="maximum_order">Max Order Per Customer</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-shopping-cart"></i></span>
                                        </div>
                                        <input id="maximum_order" name="maximum_order" type="number" value="{{ old('maximum_order', $product->maximum_order ?? '') }}" class="form-control" />
                                    </div>
                                    <small class="form-text text-muted">Leave empty for unlimited</small>
                                    @error('maximum_order')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Description Card --}}
                <div class="card card-info card-outline shadow-sm">
                    <div class="card-header border-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-align-left text-info mr-2"></i>
                            <h3 class="card-title m-0 font-weight-bold">Description</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="short_description">Short Description</label>
                                    <textarea id="short_description" name="short_description" rows="3" class="form-control" placeholder="Brief product summary...">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                                    <small class="form-text text-muted">Displayed in product listings (max 500 characters)</small>
                                    @error('short_description')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Full Description</label>
                                    <textarea id="description" name="description" rows="6" class="form-control" placeholder="Detailed product information...">{{ old('description', $product->description ?? '') }}</textarea>
                                    @error('description')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEO Card --}}
                <div class="card card-warning card-outline shadow-sm">
                    <div class="card-header border-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-search text-warning mr-2"></i>
                            <h3 class="card-title m-0 font-weight-bold">SEO Settings</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="meta_title">Meta Title</label>
                                    <input id="meta_title" name="meta_title" type="text" value="{{ old('meta_title', $product->meta_title ?? '') }}" class="form-control" placeholder="SEO title for search engines" />
                                    <small class="form-text text-muted">Recommended: 50-60 characters</small>
                                    @error('meta_title')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="meta_description">Meta Description</label>
                                    <textarea id="meta_description" name="meta_description" rows="3" class="form-control" placeholder="SEO description for search results...">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
                                    <small class="form-text text-muted">Recommended: 150-160 characters</small>
                                    @error('meta_description')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="meta_keywords">Meta Keywords</label>
                                    <input id="meta_keywords" name="meta_keywords" type="text" value="{{ old('meta_keywords', $product->meta_keywords ?? '') }}" class="form-control" placeholder="keyword1, keyword2, keyword3" />
                                    <small class="form-text text-muted">Separate keywords with commas</small>
                                    @error('meta_keywords')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Publish Card --}}
                <div class="card card-primary shadow-sm">
                    <div class="card-header border-0">
                        <h3 class="card-title m-0 font-weight-bold"><i class="fas fa-bolt text-warning"></i> Publish</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="visibility">Visibility</label>
                            <select id="visibility" name="visibility" class="form-control select2" required>
                                <option value="public" {{ old('visibility', $product->visibility ?? 'public') == 'public' ? 'selected' : '' }}>
                                    <i class="fas fa-globe text-success"></i> Public (Visible everywhere)
                                </option>
                                <option value="private" {{ old('visibility', $product->visibility ?? '') == 'private' ? 'selected' : '' }}>
                                    <i class="fas fa-lock text-secondary"></i> Private (Admin only)
                                </option>
                                <option value="hidden" {{ old('visibility', $product->visibility ?? '') == 'hidden' ? 'selected' : '' }}>
                                    <i class="fas fa-eye-slash text-info"></i> Hidden (Not in listings)
                                </option>
                            </select>
                            @error('visibility')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" id="featured" name="featured" class="custom-control-input" value="1" {{ old('featured', $product->featured ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="featured">
                                    <i class="fas fa-star text-warning"></i> Featured Product
                                </label>
                            </div>
                            <small class="form-text text-muted">Show on homepage featured section</small>
                            @error('featured')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                        </div>
                        <hr>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-success btn-block btn-lg">
                                <i class="fas fa-check-circle"></i> {{ empty($product) ? 'Create Product' : 'Update Product' }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Thumbnail Card --}}
                <div class="card card-secondary shadow-sm">
                    <div class="card-header border-0">
                        <h3 class="card-title m-0 font-weight-bold"><i class="fas fa-camera text-primary"></i> Thumbnail</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label for="thumbnail">Product Thumbnail <span class="text-danger">*</span></label>
                            <div class="thumbnail-upload-container text-center" id="thumbnail-container">
                                <div class="thumbnail-preview" id="thumbnail-preview">
                                    @if(!empty($product) && $product->thumbnail)
                                        <img id="thumbnail-image" src="{{ asset('storage/' . $product->thumbnail) }}" alt="Thumbnail" class="img-fluid" />
                                    @else
                                        <div class="thumbnail-placeholder">
                                            <i class="fas fa-image fa-4x text-muted"></i>
                                            <p class="mt-2 mb-0 text-muted">No image</p>
                                        </div>
                                    @endif
                                </div>
                                <input id="thumbnail" name="thumbnail" type="file" class="d-none" accept="image/*" />
                                <button type="button" class="btn btn-primary btn-sm btn-block mt-2" id="thumbnail-upload-btn">
                                    <i class="fas fa-upload"></i> Upload Image
                                </button>
                                @if(!empty($product) && $product->thumbnail)
                                    <button type="button" class="btn btn-danger btn-sm btn-block mt-1" id="thumbnail-remove-btn">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                @endif
                            </div>
                            <small class="form-text text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i> Recommended: 500x500px or higher<br>
                                <i class="fas fa-file"></i> Max 2MB (JPG, PNG, WEBP)
                            </small>
                            @error('thumbnail')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                {{-- Gallery Card --}}
                <div class="card card-success shadow-sm">
                    <div class="card-header border-0">
                        <h3 class="card-title m-0 font-weight-bold"><i class="fas fa-images text-success"></i> Gallery</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label>Product Images</label>
                            <div class="gallery-drop-zone" id="gallery-drop-zone">
                                <div class="drop-zone-content">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                                    <p class="mt-2 mb-1"><strong>Drag & Drop</strong> images here</p>
                                    <p class="text-muted small mb-0">or click to browse</p>
                                    <span class="badge badge-info mt-2">Max 10 images</span>
                                </div>
                                <input id="gallery-input" name="images[]" type="file" class="d-none" accept="image/*" multiple />
                            </div>
                            <div class="new-gallery-preview mt-3" id="new-gallery-preview"></div>
                            @error('images')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                @if(!empty($product) && $product->images->isNotEmpty())
                <div class="card card-info shadow-sm">
                    <div class="card-header border-0">
                        <h3 class="card-title m-0 font-weight-bold">
                            <i class="fas fa-photo-video text-info"></i> Current Images
                            <span class="badge badge-secondary float-right">{{ $product->images->count() }}</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="existing-gallery-container" id="existing-gallery-container">
                            @foreach($product->images as $index => $galleryImage)
                                <div class="gallery-item" data-image-id="{{ $galleryImage->id }}">
                                    <img src="{{ asset('storage/' . $galleryImage->image) }}" alt="Gallery {{ $index + 1 }}" />
                                    <div class="gallery-item-overlay">
                                        <div class="gallery-item-actions">
                                            <button type="button" class="btn btn-sm btn-success keep-btn" data-image-id="{{ $galleryImage->id }}" title="Keep">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-image-id="{{ $galleryImage->id }}" data-image-path="{{ $galleryImage->image }}" title="Delete">
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
        </div>

        {{-- ===== VARIABLE PRODUCT VARIANTS SECTION ===== --}}
        <div id="variable-variants-section" style="display: none;">
            <div class="col-12">
                <div class="card card-primary shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-layer-group mr-2"></i>
                                <h3 class="card-title m-0">Product Variants</h3>
                            </div>
                            <span class="badge badge-light" id="variant-count">0 variants</span>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Attribute Selection --}}
                        <div class="mb-4">
                            <div class="alert alert-light border">
                                <i class="fas fa-info-circle text-primary"></i>
                                <strong>Step 1:</strong> Select attributes below to generate variant combinations automatically.
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="font-weight-bold mb-3">Select Attributes</label>
                                <div class="attribute-selector" id="attribute-selector">
                                    @foreach($attributes as $attribute)
                                        @if($attribute->type == 'select' || $attribute->type == 'color')
                                            <div class="attr-group mb-3" data-attr-id="{{ $attribute->id }}" data-attr-name="{{ $attribute->name }}" data-attr-type="{{ $attribute->type }}">
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-header bg-light d-flex align-items-center justify-content-between" style="cursor: pointer;" onclick="toggleAttrValues(this)">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-{{ $attribute->type == 'color' ? 'palette' : 'ruler' }} text-primary mr-2"></i>
                                                            <span class="font-weight-bold">{{ $attribute->name }}</span>
                                                            <span class="attr-selected-count badge badge-info ml-2">0 selected</span>
                                                        </div>
                                                        <i class="fas fa-chevron-down text-muted"></i>
                                                    </div>
                                                    <div class="attr-values card-body" style="display: none;">
                                                        <div class="attr-options">
                                                            @foreach($attribute->values as $value)
                                                                @if($attribute->type == 'color')
                                                                    <button type="button"
                                                                        class="attr-option attr-swatch"
                                                                        data-attr-name="{{ $attribute->name }}"
                                                                        data-attr-id="{{ $attribute->id }}"
                                                                        data-value="{{ $value->value }}"
                                                                        style="background-color: {{ $value->value }}; min-width: 45px; height: 45px; border: 3px solid #dee2e6; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; margin: 5px; transition: all 0.2s;">
                                                                    </button>
                                                                @else
                                                                    <button type="button"
                                                                        class="attr-option attr-pill"
                                                                        data-attr-name="{{ $attribute->name }}"
                                                                        data-attr-id="{{ $attribute->id }}"
                                                                        data-value="{{ $value->value }}"
                                                                        style="min-width: 90px; height: 42px; border: 2px solid #dee2e6; border-radius: 25px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; margin: 5px; padding: 0 18px; transition: all 0.2s; background: #fff; color: #495057; font-weight: 500;">
                                                                        {{ $value->value }}
                                                                    </button>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Generate Button (Create Mode Only) --}}
                        @if(empty($product))
                        <div class="row mb-4">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary btn-lg px-5" id="generate-variants-btn">
                                    <i class="fas fa-magic mr-2"></i>Generate All Variants
                                </button>
                                <p class="text-muted mt-2 mb-0"><small>Click to create all combinations from selected attributes</small></p>
                            </div>
                        </div>
                        @endif

                        {{-- Add New Variant Button (Edit Mode) --}}
                        <div class="row mb-4" id="add-variant-row" style="display: none;">
                            <div class="col-12">
                                <button type="button" class="btn btn-success btn-lg px-5" id="add-new-variant-btn">
                                    <i class="fas fa-plus mr-2"></i>Add New Variant
                                </button>
                                <p class="text-muted mt-2 mb-0"><small>Add variants one by one</small></p>
                            </div>
                        </div>

                        {{-- Variants Table --}}
                        <div id="variants-editor" style="display: none;">
                            <div class="variant-summary-bar mt-4 mb-4 p-3 bg-gradient-primary rounded text-white">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-layer-group fa-2x mr-3 opacity-75"></i>
                                            <div>
                                                <small class="opacity-75">Total Variants</small>
                                                <div class="h3 mb-0" id="summary-total">0</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-boxes fa-2x mr-3 opacity-75"></i>
                                            <div>
                                                <small class="opacity-75">Total Stock</small>
                                                <div class="h3 mb-0 text-warning" id="summary-stock">0</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle fa-2x mr-3 opacity-75"></i>
                                            <div>
                                                <small class="opacity-75">In Stock</small>
                                                <div class="h3 mb-0 text-success" id="summary-instock">0</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <button type="button" class="btn btn-outline-light btn-sm" id="clear-all-variants">
                                            <i class="fas fa-trash mr-1"></i> Clear All
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover variant-table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width: 200px;">Attributes</th>
                                            <th style="width: 120px;">SKU</th>
                                            <th style="width: 120px;">Price (৳)</th>
                                            <th style="width: 100px;">Stock</th>
                                            <th style="width: 100px;">Status</th>
                                            <th style="width: 180px;">Variant Image</th>
                                            <th style="width: 70px;">Action</th>
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
        </div>
        {{-- ===== END VARIABLE PRODUCT VARIANTS SECTION ===== --}}
    </form>
</div>
@endsection

@push('styles')
<style>
.card {
    border: none;
    border-radius: 8px;
}

.card-header {
    background: transparent !important;
}

.form-group label {
    font-weight: 600;
    color: #343a40;
    margin-bottom: 0.5rem;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.select2-container--default .select2-selection--single {
    border: 1px solid #ced4da;
    height: 38px;
    border-radius: 0.25rem;
}

.select2-container--default .select2-selection--single:focus {
    border-color: #667eea;
}

/* Attribute Selector Styles */
.attr-group {
    margin-bottom: 15px;
}

.attr-group .card {
    border-radius: 8px;
    overflow: hidden;
}

.attr-group .card-header {
    background: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6;
    padding: 12px 15px;
}

.attr-group .card-header:hover {
    background: #e9ecef !important;
}

.attr-group .card-header i.fa-chevron-down {
    transition: transform 0.3s ease;
}

.attr-group .card-header.open i.fa-chevron-down {
    transform: rotate(180deg);
}

.attr-values {
    padding: 15px;
    background: #fff;
}

.attr-options {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.attr-option {
    user-select: none;
    font-size: 14px;
}

.attr-option.selected {
    border-color: #28a745 !important;
    transform: scale(1.05);
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.3);
}

.attr-pill.selected {
    background: #d4edda !important;
}

.attr-swatch.selected {
    box-shadow: 0 0 0 4px #28a745, 0 0 0 8px rgba(40, 167, 69, 0.2);
    background-color: inherit !important;
}

/* Variant Summary Bar */
.variant-summary-bar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.opacity-75 {
    opacity: 0.75;
}

/* Variants Table */
.variant-table thead {
    background: #343a40;
    color: #fff;
}

.variant-table tbody tr:hover {
    background: #f8f9fa;
}

.variant-table tbody tr.variant-row {
    border-left: 3px solid transparent;
}

.variant-table tbody tr.variant-row.has-stock {
    border-left-color: #28a745;
}

.variant-attributes-display {
    font-size: 13px;
}

.variant-attribute-badge {
    display: inline-block;
    padding: 4px 10px;
    margin: 2px;
    background: #e9ecef;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 500;
    color: #495057;
}

.variant-attribute-badge .attr-name {
    font-weight: 600;
    color: #667eea;
}

/* Thumbnail Upload */
.thumbnail-upload-container {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 15px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.thumbnail-upload-container:hover {
    border-color: #667eea;
    background: #f0f4ff;
}

.thumbnail-preview {
    width: 100%;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border-radius: 6px;
    overflow: hidden;
}

.thumbnail-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.thumbnail-placeholder {
    text-align: center;
    color: #6c757d;
}

/* Gallery Drop Zone */
.gallery-drop-zone {
    border: 3px dashed #dee2e6;
    border-radius: 12px;
    padding: 30px 20px;
    text-align: center;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s ease;
}

.gallery-drop-zone:hover,
.gallery-drop-zone.drag-over {
    border-color: #28a745;
    background: #f0fff4;
}

.gallery-drop-zone .drop-zone-content i {
    color: #667eea;
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
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.new-gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.new-gallery-item .remove-btn {
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
}

/* Existing Gallery */
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
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
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
    background: rgba(0,0,0,0.7);
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
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 12px;
}

/* Category Tree */
.category-checkbox-tree {
    background: #fff;
}

/* Button Styles */
.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
}

.btn-success:hover {
    background: linear-gradient(135deg, #218838 0%, #1aa179 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd6 0%, #6a4190 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}
</style>
@endpush

@push('scripts')
<script>
// Toggle attribute values visibility
function toggleAttrValues(header) {
    const attrGroup = header.closest('.attr-group');
    const attrValues = attrGroup.querySelector('.attr-values');
    const icon = header.querySelector('.fa-chevron-down');

    if (attrValues.style.display === 'none') {
        attrValues.style.display = 'block';
        header.classList.add('open');
    } else {
        attrValues.style.display = 'none';
        header.classList.remove('open');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    if (typeof Select2 !== 'undefined') {
        $('.select2').select2({
            dropdownParent: $('body'),
            width: '100%'
        });
    }

    // ===== VARIABLE PRODUCT HANDLING =====
    const productTypeSelect = document.getElementById('product_type');
    const variableVariantsSection = document.getElementById('variable-variants-section');
    const variantsTableBody = document.getElementById('variants-table-body');
    const variantsEditor = document.getElementById('variants-editor');

    if (productTypeSelect && variableVariantsSection) {
        productTypeSelect.addEventListener('change', function() {
            if (this.value === 'variable') {
                variableVariantsSection.style.display = 'block';
            } else {
                variableVariantsSection.style.display = 'none';
            }
        });

        // Show variants section if editing a variable product
        if (productTypeSelect.value === 'variable') {
            variableVariantsSection.style.display = 'block';
        }
    }

    // ===== EDIT MODE CHECK =====
    const isEditMode = @json(!empty($product));

    // ===== LOAD EXISTING VARIANTS ON EDIT =====
    @php
        $existingVariantsData = [];
        if (!empty($product) && $product->product_type == 'variable' && $product->variants->isNotEmpty()) {
            $existingVariantsData = $product->variants->map(function($v) {
                return [
                    'id' => $v->id,
                    'attributes' => $v->attributes,
                    'sku' => $v->sku,
                    'price' => $v->price,
                    'stock' => $v->stock,
                ];
            })->toArray();
        }
    @endphp
    @if(count($existingVariantsData) > 0)
    // Existing variants data from server
    const existingVariants = @json($existingVariantsData);

    console.log('Loading existing variants:', existingVariants);

    // Load existing variants into the editor
    if (existingVariants && existingVariants.length > 0) {
        console.log('Calling loadExistingVariants with:', existingVariants.length, 'variants');
        loadExistingVariants(existingVariants);
    } else {
        console.log('No variants to load');
    }

    // Show Add New Variant button in edit mode
    document.getElementById('add-variant-row').style.display = 'block';
    @endif

    function loadExistingVariants(variants) {
        variantsTableBody.innerHTML = '';

        variants.forEach((variant, index) => {
            const row = document.createElement('tr');
            row.className = 'variant-row';
            if (variant.stock > 0) {
                row.classList.add('has-stock');
            }
            row.dataset.index = index;
            if (variant.id) {
                row.dataset.variantId = variant.id;
            }

            // Build attributes badges
            const attrsHtml = Object.entries(variant.attributes || {}).map(([name, value]) =>
                `<span class="variant-attribute-badge"><span class="attr-name">${name}:</span> ${value}</span>`
            ).join('');

            const isActive = variant.stock > 0;

            row.innerHTML = `
                <td>
                    <div class="variant-attributes-display">${attrsHtml}</div>
                </td>
                <td>
                    <input type="text" name="variants[${index}][sku]" class="form-control form-control-sm variant-sku-input" value="${variant.sku || ''}" placeholder="SKU" />
                </td>
                <td>
                    <input type="number" name="variants[${index}][price]" step="0.01" class="form-control form-control-sm variant-price-input" value="${variant.price || ''}" placeholder="0.00" />
                </td>
                <td>
                    <input type="number" name="variants[${index}][stock]" class="form-control form-control-sm variant-stock-input" value="${variant.stock || 0}" min="0" />
                </td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="variants[${index}][active]" class="custom-control-input variant-active-checkbox" id="variant-active-${index}" ${isActive ? 'checked' : ''} value="1">
                        <label class="custom-control-label" for="variant-active-${index}">
                            <i class="fas fa-${isActive ? 'check' : 'times'}"></i>
                        </label>
                    </div>
                </td>
                <td>
                    <input type="file" name="variants[${index}][image][]" class="form-control form-control-sm" accept="image/*" multiple />
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-variant">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            variantsTableBody.appendChild(row);

            // Add stock change listener
            const stockInput = row.querySelector('.variant-stock-input');
            const activeCheckbox = row.querySelector('.variant-active-checkbox');

            stockInput.addEventListener('change', function() {
                const stock = parseInt(this.value) || 0;
                if (stock > 0) {
                    row.classList.add('has-stock');
                    activeCheckbox.checked = true;
                } else {
                    row.classList.remove('has-stock');
                    activeCheckbox.checked = false;
                }
                updateVariantSummary();
            });

            activeCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    row.classList.add('has-stock');
                } else {
                    row.classList.remove('has-stock');
                }
                updateVariantSummary();
            });
        });

        updateVariantSummary();
        variantsEditor.style.display = 'block';

        // Add event listeners for remove buttons
        document.querySelectorAll('.remove-variant').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Remove this variant?')) {
                    this.closest('tr').remove();
                    updateVariantSummary();
                }
            });
        });
    }
    @endif

    // ===== ATTRIBUTE SELECTION =====
    const selectedAttributes = {};

    document.querySelectorAll('.attr-option').forEach(option => {
        option.addEventListener('click', function() {
            const attrName = this.dataset.attrName;
            const attrId = this.dataset.attrId;
            const value = this.dataset.value;

            this.classList.toggle('selected');

            if (!selectedAttributes[attrId]) {
                selectedAttributes[attrId] = { name: attrName, values: [] };
            }

            if (this.classList.contains('selected')) {
                selectedAttributes[attrId].values.push(value);
            } else {
                selectedAttributes[attrId].values = selectedAttributes[attrId].values.filter(v => v !== value);
                if (selectedAttributes[attrId].values.length === 0) {
                    delete selectedAttributes[attrId];
                }
            }

            // Update count
            const attrGroup = this.closest('.attr-group');
            const countSpan = attrGroup.querySelector('.attr-selected-count');
            countSpan.textContent = Object.keys(selectedAttributes).length + ' selected';
        });
    });

    // Generate Variants
    const generateVariantsBtn = document.getElementById('generate-variants-btn');

    if (generateVariantsBtn) {
        generateVariantsBtn.addEventListener('click', function() {
            const attrIds = Object.keys(selectedAttributes);

            if (attrIds.length === 0) {
                alert('Please select at least one attribute with values.');
                return;
            }

            // Generate all combinations
            const combinations = generateCombinations(selectedAttributes);

            // Render variants table
            renderVariantsTable(combinations);

            variantsEditor.style.display = 'block';
        });
    }

    function generateCombinations(attributes) {
        const attrKeys = Object.keys(attributes);
        let combinations = [[]];

        for (const key of attrKeys) {
            const current = combinations;
            combinations = [];
            for (const combo of current) {
                for (const value of attributes[key].values) {
                    combinations.push([...combo, { name: attributes[key].name, value: value }]);
                }
            }
        }

        return combinations;
    }

    function renderVariantsTable(combinations) {
        variantsTableBody.innerHTML = '';

        combinations.forEach((combo, index) => {
            const row = document.createElement('tr');
            row.className = 'variant-row';
            row.dataset.index = index;

            const attrsHtml = combo.map(attr =>
                `<span class="variant-attribute-badge"><span class="attr-name">${attr.name}:</span> ${attr.value}</span>`
            ).join('');

            row.innerHTML = `
                <td>
                    <div class="variant-attributes-display">${attrsHtml}</div>
                </td>
                <td>
                    <input type="text" name="variants[${index}][sku]" class="form-control form-control-sm" placeholder="SKU" />
                </td>
                <td>
                    <input type="number" name="variants[${index}][price]" step="0.01" class="form-control form-control-sm" placeholder="0.00" />
                </td>
                <td>
                    <input type="number" name="variants[${index}][stock]" class="form-control form-control-sm variant-stock-input" value="0" min="0" />
                </td>
                <td>
                    <select name="variants[${index}][status]" class="form-control form-control-sm variant-status-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </td>
                <td>
                    <input type="file" name="variants[${index}][image][]" class="form-control form-control-sm" accept="image/*" multiple />
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-variant">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            variantsTableBody.appendChild(row);

            // Add stock change listener
            const stockInput = row.querySelector('.variant-stock-input');
            const statusSelect = row.querySelector('.variant-status-select');

            stockInput.addEventListener('change', function() {
                const stock = parseInt(this.value) || 0;
                if (stock > 0) {
                    row.classList.add('has-stock');
                    statusSelect.value = 'active';
                } else {
                    row.classList.remove('has-stock');
                }
                updateVariantSummary();
            });

            statusSelect.addEventListener('change', function() {
                if (this.value === 'active') {
                    row.classList.add('has-stock');
                } else {
                    row.classList.remove('has-stock');
                }
                updateVariantSummary();
            });
        });

        updateVariantSummary();

        // Add event listeners for remove buttons
        document.querySelectorAll('.remove-variant').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Remove this variant?')) {
                    this.closest('tr').remove();
                    updateVariantSummary();
                }
            });
        });
    }

    function updateVariantSummary() {
        const totalVariants = variantsTableBody.querySelectorAll('tr').length;
        let totalStock = 0;

        variantsTableBody.querySelectorAll('tr').forEach(row => {
            const stockInput = row.querySelector('.variant-stock-input');
            if (stockInput) {
                totalStock += parseInt(stockInput.value) || 0;
            }
        });

        const inStock = variantsTableBody.querySelectorAll('tr.has-stock').length;

        document.getElementById('summary-total').textContent = totalVariants;
        document.getElementById('summary-stock').textContent = totalStock;
        document.getElementById('summary-instock').textContent = inStock;
        document.getElementById('variant-count').textContent = totalVariants + ' variants';
    }

    // Clear all variants
    const clearAllBtn = document.getElementById('clear-all-variants');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to clear all variants?')) {
                variantsTableBody.innerHTML = '';
                variantsEditor.style.display = 'none';
                updateVariantSummary();
            }
        });
    }

    // ===== ADD NEW VARIANT BUTTON (EDIT MODE) =====
    const addNewVariantBtn = document.getElementById('add-new-variant-btn');
    if (addNewVariantBtn) {
        addNewVariantBtn.addEventListener('click', function() {
            addNewVariantRow();
        });
    }

    function addNewVariantRow() {
        const index = variantsTableBody.querySelectorAll('tr').length;

        const row = document.createElement('tr');
        row.className = 'variant-row';
        row.dataset.index = index;

        row.innerHTML = `
            <td>
                <div class="form-group mb-2">
                    <label class="small font-weight-bold">Attribute 1</label>
                    <select name="variants[${index}][attr1_name]" class="form-control form-control-sm variant-attr-name" onchange="updateVariantRow(this)">
                        <option value="">Select Attribute</option>
                        @foreach($attributes as $attribute)
                            <option value="{{ $attribute->name }}">{{ $attribute->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2">
                    <input type="text" name="variants[${index}][attr1_value]" class="form-control form-control-sm" placeholder="Value (e.g., Red)" />
                </div>
                <div class="form-group mb-0">
                    <label class="small font-weight-bold">Attribute 2 (Optional)</label>
                    <select name="variants[${index}][attr2_name]" class="form-control form-control-sm variant-attr-name" onchange="updateVariantRow(this)">
                        <option value="">-- Select --</option>
                        @foreach($attributes as $attribute)
                            <option value="{{ $attribute->name }}">{{ $attribute->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0">
                    <input type="text" name="variants[${index}][attr2_value]" class="form-control form-control-sm" placeholder="Value (e.g., Large)" />
                </div>
            </td>
            <td>
                <input type="text" name="variants[${index}][sku]" class="form-control form-control-sm variant-sku-input" placeholder="SKU" />
            </td>
            <td>
                <input type="number" name="variants[${index}][price]" step="0.01" class="form-control form-control-sm variant-price-input" placeholder="0.00" />
            </td>
            <td>
                <input type="number" name="variants[${index}][stock]" class="form-control form-control-sm variant-stock-input" value="0" min="0" />
            </td>
            <td>
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="variants[${index}][active]" class="custom-control-input variant-active-checkbox" id="variant-active-new-${index}" value="1">
                    <label class="custom-control-label" for="variant-active-new-${index}">
                        <i class="fas fa-times"></i>
                    </label>
                </div>
            </td>
            <td>
                <input type="file" name="variants[${index}][image][]" class="form-control form-control-sm" accept="image/*" multiple />
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-variant">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        variantsTableBody.appendChild(row);

        // Add stock change listener
        const stockInput = row.querySelector('.variant-stock-input');
        const activeCheckbox = row.querySelector('.variant-active-checkbox');

        stockInput.addEventListener('change', function() {
            const stock = parseInt(this.value) || 0;
            if (stock > 0) {
                row.classList.add('has-stock');
                activeCheckbox.checked = true;
            } else {
                row.classList.remove('has-stock');
                activeCheckbox.checked = false;
            }
            updateVariantSummary();
        });

        activeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                row.classList.add('has-stock');
            } else {
                row.classList.remove('has-stock');
            }
            updateVariantSummary();
        });

        // Add event listeners for remove buttons
        row.querySelector('.remove-variant').addEventListener('click', function() {
            if (confirm('Remove this variant?')) {
                row.remove();
                updateVariantSummary();
            }
        });

        variantsEditor.style.display = 'block';
        updateVariantSummary();
    }

    // Generate Slug from Name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const generateSlugBtn = document.getElementById('generate-slug');

    if (generateSlugBtn && nameInput && slugInput) {
        generateSlugBtn.addEventListener('click', function() {
            const name = nameInput.value;
            slugInput.value = name.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)/g, '');
        });
    }

    // Generate SKU
    const generateSkuBtn = document.getElementById('generate-sku');
    const skuInput = document.getElementById('sku');

    if (generateSkuBtn && skuInput) {
        generateSkuBtn.addEventListener('click', function() {
            const timestamp = Date.now().toString(36).toUpperCase();
            const random = Math.random().toString(36).substring(2, 6).toUpperCase();
            skuInput.value = 'SKU-' + timestamp + '-' + random;
        });
    }

    // Thumbnail Upload
    const thumbnailInput = document.getElementById('thumbnail');
    const thumbnailPreview = document.getElementById('thumbnail-preview');
    const thumbnailUploadBtn = document.getElementById('thumbnail-upload-btn');
    const thumbnailRemoveBtn = document.getElementById('thumbnail-remove-btn');
    const thumbnailImage = document.getElementById('thumbnail-image');

    if (thumbnailUploadBtn && thumbnailInput) {
        thumbnailUploadBtn.addEventListener('click', function() {
            thumbnailInput.click();
        });
    }

    if (thumbnailInput) {
        thumbnailInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (!thumbnailImage) {
                        const img = document.createElement('img');
                        img.id = 'thumbnail-image';
                        img.src = e.target.result;
                        img.className = 'img-fluid';
                        thumbnailPreview.innerHTML = '';
                        thumbnailPreview.appendChild(img);
                    } else {
                        thumbnailImage.src = e.target.result;
                    }

                    if (thumbnailRemoveBtn) {
                        thumbnailRemoveBtn.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (thumbnailRemoveBtn) {
        thumbnailRemoveBtn.addEventListener('click', function() {
            if (thumbnailInput) thumbnailInput.value = '';
            thumbnailPreview.innerHTML = `
                <div class="thumbnail-placeholder">
                    <i class="fas fa-image fa-4x text-muted"></i>
                    <p class="mt-2 mb-0 text-muted">No image</p>
                </div>
            `;
            this.style.display = 'none';
        });
    }

    // Gallery Drop Zone
    const galleryDropZone = document.getElementById('gallery-drop-zone');
    const galleryInput = document.getElementById('gallery-input');
    const newGalleryPreview = document.getElementById('new-gallery-preview');

    if (galleryDropZone && galleryInput) {
        galleryDropZone.addEventListener('click', function() {
            galleryInput.click();
        });

        galleryDropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        galleryDropZone.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });

        galleryDropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            handleGalleryFiles(files);
        });

        galleryInput.addEventListener('change', function(e) {
            const files = e.target.files;
            handleGalleryFiles(files);
        });
    }

    function handleGalleryFiles(files) {
        if (!newGalleryPreview) return;

        Array.from(files).slice(0, 10 - newGalleryPreview.children.length).forEach(file => {
            if (file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'new-gallery-item';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Gallery image" />
                        <button type="button" class="remove-btn">&times;</button>
                    `;
                    div.querySelector('.remove-btn').addEventListener('click', function() {
                        div.remove();
                    });
                    newGalleryPreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Existing Gallery Delete
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const imageId = this.dataset.imageId;
            const galleryItem = this.closest('.gallery-item');

            if (confirm('Are you sure you want to delete this image?')) {
                let deletedInput = document.querySelector('input[name="deleted_images[]"]');
                if (!deletedInput) {
                    deletedInput = document.createElement('input');
                    deletedInput.type = 'hidden';
                    deletedInput.name = 'deleted_images[]';
                    document.getElementById('productForm').appendChild(deletedInput);
                }
                deletedInput.value = (deletedInput.value ? deletedInput.value + ',' : '') + imageId;

                galleryItem.remove();
            }
        });
    });

    // Keep button for existing images
    document.querySelectorAll('.keep-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const checkbox = this.closest('.gallery-item').querySelector('.keep-checkbox');
            if (checkbox) {
                checkbox.checked = true;
            }
            this.classList.add('btn-light');
            this.classList.remove('btn-success');
        });
    });
});
</script>
@endpush