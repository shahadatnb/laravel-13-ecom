@extends('admin.layouts.app')
@section('title', empty($category) ? 'Create Category' : 'Edit Category')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ empty($category) ? 'Create Category' : 'Edit Category' }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.category.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')

                <form method="POST" action="{{ empty($category) ? route('admin.category.store') : route('admin.category.update', $category->id) }}" enctype="multipart/form-data">
                    @csrf
                    @if(!empty($category))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Category Name <span class="text-danger">*</span></label>
                                <input id="name" name="name" type="text" value="{{ old('name', $category->name ?? '') }}" required class="form-control" placeholder="Category Name" />
                                @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name_bn">Name (Bengali)</label>
                                <input id="name_bn" name="name_bn" type="text" value="{{ old('name_bn', $category->name_bn ?? '') }}" class="form-control" placeholder="বাংলা নাম" />
                                @error('name_bn')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="parent_id">Parent Category</label>
                                <select id="parent_id" name="parent_id" class="form-control">
                                    <option value="">-- Root Category --</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id', isset($category) && $category->parent_id == $parent->id ? $category->parent_id : '') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="slug">Slug <span class="text-danger">*</span></label>
                                <input id="slug" name="slug" type="text" value="{{ old('slug', $category->slug ?? '') }}" required class="form-control" placeholder="category-slug" />
                                @error('slug')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="featured">Featured</label>
                                <div class="custom-control custom-switch mt-2">
                                    <input type="hidden" name="featured" value="0" />
                                    <input type="checkbox" class="custom-control-input" id="featured" name="featured" value="1" {{ old('featured', $category->featured ?? 0) ? 'checked' : '' }} />
                                    <label class="custom-control-label" for="featured">Featured on Homepage</label>
                                </div>
                                @error('featured')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status', $category->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $category->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="archived" {{ old('status', $category->status ?? '') == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                @error('status')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="visibility">Visibility <span class="text-danger">*</span></label>
                                <select id="visibility" name="visibility" class="form-control" required>
                                    <option value="">Select Visibility</option>
                                    <option value="public" {{ old('visibility', $category->visibility ?? '') == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="hidden" {{ old('visibility', $category->visibility ?? '') == 'hidden' ? 'selected' : '' }}>Hidden</option>
                                    <option value="menu_only" {{ old('visibility', $category->visibility ?? '') == 'menu_only' ? 'selected' : '' }}>Menu Only</option>
                                    <option value="homepage" {{ old('visibility', $category->visibility ?? '') == 'homepage' ? 'selected' : '' }}>Homepage</option>
                                </select>
                                @error('visibility')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sort_order">Sort Order</label>
                                <input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="form-control" />
                                @error('sort_order')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="icon">Icon</label>
                                <input id="icon" name="icon" type="file" class="form-control" accept="image/*" />
                                @if(!empty($category) && $category->icon && Storage::disk('public')->exists($category->icon))
                                    <img src="{{ asset('storage/' . $category->icon) }}" width="40" height="40" class="mt-2" style="object-fit:contain;" />
                                @endif
                                @error('icon')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="thumbnail">Thumbnail</label>
                                <input id="thumbnail" name="thumbnail" type="file" class="form-control" accept="image/*" />
                                @if(!empty($category) && $category->thumbnail && Storage::disk('public')->exists($category->thumbnail))
                                    <img src="{{ asset('storage/' . $category->thumbnail) }}" width="80" height="60" class="mt-2" style="object-fit:contain;" />
                                @endif
                                @error('thumbnail')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="banner">Banner</label>
                                <input id="banner" name="banner" type="file" class="form-control" accept="image/*" />
                                @if(!empty($category) && $category->banner && Storage::disk('public')->exists($category->banner))
                                    <img src="{{ asset('storage/' . $category->banner) }}" width="120" height="60" class="mt-2" style="object-fit:contain;" />
                                @endif
                                @error('banner')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="short_description">Short Description</label>
                                <textarea id="short_description" name="short_description" rows="2" class="form-control" placeholder="Short description">{{ old('short_description', $category->short_description ?? '') }}</textarea>
                                @error('short_description')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="2" class="form-control" placeholder="Full description">{{ old('description', $category->description ?? '') }}</textarea>
                                @error('description')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="meta_title">Meta Title</label>
                                <input id="meta_title" name="meta_title" type="text" value="{{ old('meta_title', $category->meta_title ?? '') }}" class="form-control" placeholder="SEO Title" />
                                @error('meta_title')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="meta_keywords">Meta Keywords</label>
                                <input id="meta_keywords" name="meta_keywords" type="text" value="{{ old('meta_keywords', $category->meta_keywords ?? '') }}" class="form-control" placeholder="keyword1, keyword2" />
                                @error('meta_keywords')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="meta_description">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" rows="2" class="form-control" placeholder="SEO description">{{ old('meta_description', $category->meta_description ?? '') }}</textarea>
                        @error('meta_description')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">{{ empty($category) ? 'Create' : 'Update' }}</button>
                        <a href="{{ route('admin.category.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        });
    }
});
</script>
@endsection

@endsection
