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
</div>