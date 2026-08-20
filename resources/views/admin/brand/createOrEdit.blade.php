@extends('admin.layouts.app')
@section('title', empty($brand) ? 'Create Brand' : 'Edit Brand')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ empty($brand) ? 'Create Brand' : 'Edit Brand' }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.brand.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')

                <form method="POST" action="{{ empty($brand) ? route('admin.brand.store') : route('admin.brand.update', $brand->id) }}" enctype="multipart/form-data">
                    @csrf
                    @if(!empty($brand))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Brand Name <span class="text-danger">*</span></label>
                                <input id="name" name="name" type="text" value="{{ old('name', $brand->name ?? '') }}" required class="form-control" placeholder="Brand Name" />
                                @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="slug">Slug <span class="text-danger">*</span></label>
                                <input id="slug" name="slug" type="text" value="{{ old('slug', $brand->slug ?? '') }}" required class="form-control" placeholder="brand-slug" />
                                @error('slug')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status', $brand->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $brand->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="archived" {{ old('status', $brand->status ?? '') == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                @error('status')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="visibility">Visibility <span class="text-danger">*</span></label>
                                <select id="visibility" name="visibility" class="form-control" required>
                                    <option value="">Select Visibility</option>
                                    <option value="public" {{ old('visibility', $brand->visibility ?? '') == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="hidden" {{ old('visibility', $brand->visibility ?? '') == 'hidden' ? 'selected' : '' }}>Hidden</option>
                                    <option value="homepage" {{ old('visibility', $brand->visibility ?? '') == 'homepage' ? 'selected' : '' }}>Homepage</option>
                                    <option value="featured" {{ old('visibility', $brand->visibility ?? '') == 'featured' ? 'selected' : '' }}>Featured</option>
                                </select>
                                @error('visibility')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="logo">Logo</label>
                                <input id="logo" name="logo" type="file" class="form-control" accept="image/*" />
                                @if(!empty($brand) && $brand->logo && Storage::disk('public')->exists($brand->logo))
                                    <img src="{{ asset('storage/' . $brand->logo) }}" width="100" class="mt-2" style="max-height:60px;object-fit:contain;" />
                                @endif
                                @error('logo')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="banner">Banner</label>
                                <input id="banner" name="banner" type="file" class="form-control" accept="image/*" />
                                @if(!empty($brand) && $brand->banner && Storage::disk('public')->exists($brand->banner))
                                    <img src="{{ asset('storage/' . $brand->banner) }}" width="150" class="mt-2" style="max-height:80px;object-fit:contain;" />
                                @endif
                                @error('banner')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="short_description">Short Description</label>
                                <textarea id="short_description" name="short_description" rows="2" class="form-control" placeholder="Short description">{{ old('short_description', $brand->short_description ?? '') }}</textarea>
                                @error('short_description')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="2" class="form-control" placeholder="Full description">{{ old('description', $brand->description ?? '') }}</textarea>
                                @error('description')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="website">Website</label>
                                <input id="website" name="website" type="text" value="{{ old('website', $brand->website ?? '') }}" class="form-control" placeholder="https://brand-website.com" />
                                @error('website')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="country">Country</label>
                                <input id="country" name="country" type="text" value="{{ old('country', $brand->country ?? '') }}" class="form-control" placeholder="Country" />
                                @error('country')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email', $brand->email ?? '') }}" class="form-control" placeholder="brand@example.com" />
                                @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input id="phone" name="phone" type="text" value="{{ old('phone', $brand->phone ?? '') }}" class="form-control" placeholder="Phone" />
                                @error('phone')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sort_order">Sort Order</label>
                                <input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $brand->sort_order ?? 0) }}" class="form-control" />
                                @error('sort_order')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="featured">Featured</label>
                                <select id="featured" name="featured" class="form-control">
                                    <option value="0" {{ old('featured', $brand->featured ?? false) == false ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('featured', $brand->featured ?? false) == true ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('featured')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="meta_title">Meta Title</label>
                                <input id="meta_title" name="meta_title" type="text" value="{{ old('meta_title', $brand->meta_title ?? '') }}" class="form-control" placeholder="SEO Title" />
                                @error('meta_title')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="meta_keywords">Meta Keywords</label>
                                <input id="meta_keywords" name="meta_keywords" type="text" value="{{ old('meta_keywords', $brand->meta_keywords ?? '') }}" class="form-control" placeholder="keyword1, keyword2" />
                                @error('meta_keywords')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="2" class="form-control" placeholder="Brand address">{{ old('address', $brand->address ?? '') }}</textarea>
                        @error('address')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="meta_description">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" rows="2" class="form-control" placeholder="SEO description">{{ old('meta_description', $brand->meta_description ?? '') }}</textarea>
                        @error('meta_description')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">{{ empty($brand) ? 'Create' : 'Update' }}</button>
                        <a href="{{ route('admin.brand.index') }}" class="btn btn-secondary">Cancel</a>
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
