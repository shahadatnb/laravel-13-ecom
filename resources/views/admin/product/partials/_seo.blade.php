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
    @if(!empty($product))
        <button type="submit" class="btn btn-success">Update</button>
    @else
        <button type="submit" class="btn btn-success">Create</button>
    @endif
    <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">Cancel</a>
</div>
</div>