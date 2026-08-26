@extends('admin.layouts.app')

@section('title', 'Hero Slides')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add New Slide</h3>
                    </div>
                    <form action="{{ route('admin.settings.hero-slides.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title') }}" placeholder="e.g. Summer Sale Extravaganza" required>
                                @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="subtitle">Subtitle</label>
                                <input type="text" name="subtitle" id="subtitle" class="form-control @error('subtitle') is-invalid @enderror"
                                    value="{{ old('subtitle') }}" placeholder="Up to 50% off on selected items">
                                @error('subtitle')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="badge_text">Badge Text</label>
                                <input type="text" name="badge_text" id="badge_text" class="form-control"
                                    value="{{ old('badge_text', 'Limited Time Offer') }}" placeholder="🔥 Limited Time Offer">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cta_text">Button Text</label>
                                        <input type="text" name="cta_text" id="cta_text" class="form-control"
                                            value="{{ old('cta_text', 'Shop Now') }}" placeholder="Shop Now">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cta_link">Button Link</label>
                                        <input type="text" name="cta_link" id="cta_link" class="form-control"
                                            value="{{ old('cta_link', '/products') }}" placeholder="/products">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="bg_gradient">Background Gradient</label>
                                <input type="text" name="bg_gradient" id="bg_gradient" class="form-control"
                                    value="{{ old('bg_gradient', 'from-blue-600 via-blue-700 to-indigo-900') }}"
                                    placeholder="from-blue-600 via-blue-700 to-indigo-900">
                                <small class="text-muted">Tailwind gradient classes: from-{color}-{shade} via-{color}-{shade} to-{color}-{shade}</small>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="image_emoji">Emoji Icon</label>
                                        <input type="text" name="image_emoji" id="image_emoji" class="form-control"
                                            value="{{ old('image_emoji', '🎉') }}" placeholder="🎉">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sort_order">Sort Order</label>
                                        <input type="number" name="sort_order" id="sort_order" class="form-control"
                                            value="{{ old('sort_order', 0) }}" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="bg_image">Background Image <small class="text-muted">(optional)</small></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="bg_image" name="bg_image" accept="image/*">
                                    <label class="custom-file-label" for="bg_image">Choose background image</label>
                                </div>
                                <small class="text-muted">Displayed as hero background. Recommended: 1920x800px</small>
                            </div>
                            <div class="form-group">
                                <label for="feature_image">Feature Image <small class="text-muted">(optional)</small></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="feature_image" name="feature_image" accept="image/*">
                                    <label class="custom-file-label" for="feature_image">Choose feature image</label>
                                </div>
                                <small class="text-muted">Product/feature image shown on left or right side</small>
                            </div>
                            <div class="form-group">
                                <label for="image_position">Feature Image Position</label>
                                <select name="image_position" id="image_position" class="form-control">
                                    <option value="right" {{ old('image_position', 'right') === 'right' ? 'selected' : '' }}>Right Side</option>
                                    <option value="left" {{ old('image_position') === 'left' ? 'selected' : '' }}>Left Side</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Slide
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Hero Slides</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Preview</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($slides as $slide)
                                <tr>
                                    <td>{{ $slide->sort_order }}</td>
                                    <td>
                                        @if($slide->bg_image)
                                            <img src="{{ asset('storage/' . $slide->bg_image) }}" style="width:60px;height:40px;border-radius:4px;object-fit:cover;">
                                        @else
                                            <span style="font-size:28px;">{{ $slide->image_emoji ?? '🎉' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $slide->title }}</strong>
                                        <br><small class="text-muted">{{ $slide->subtitle }}</small>
                                    </td>
                                    <td>
                                        @if($slide->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editSlide{{ $slide->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.settings.hero-slides.destroy', $slide) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Delete this slide?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editSlide{{ $slide->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.settings.hero-slides.update', $slide) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit: {{ $slide->title }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Title</label>
                                                                        <input type="text" name="title" class="form-control" value="{{ $slide->title }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Subtitle</label>
                                                                        <input type="text" name="subtitle" class="form-control" value="{{ $slide->subtitle }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Button Text</label>
                                                                        <input type="text" name="cta_text" class="form-control" value="{{ $slide->cta_text }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Button Link</label>
                                                                        <input type="text" name="cta_link" class="form-control" value="{{ $slide->cta_link }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Badge Text</label>
                                                                        <input type="text" name="badge_text" class="form-control" value="{{ $slide->badge_text }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Emoji Icon</label>
                                                                        <input type="text" name="image_emoji" class="form-control" value="{{ $slide->image_emoji }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">                                    <div class="form-group">
                                        <label>Background Gradient</label>
                                        <input type="text" name="bg_gradient" class="form-control" value="{{ $slide->bg_gradient }}">
                                        <small class="text-muted">e.g. from-blue-600 via-blue-700 to-indigo-900</small>
                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label>Sort Order</label>
                                                                        <input type="number" name="sort_order" class="form-control" value="{{ $slide->sort_order }}" min="0">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label>Active</label>
                                                                        <div class="custom-control custom-switch mt-2">
                                                                            <input type="checkbox" class="custom-control-input" id="is_active_{{ $slide->id }}"
                                                                                name="is_active" value="1" {{ $slide->is_active ? 'checked' : '' }}>
                                                                            <label class="custom-control-label" for="is_active_{{ $slide->id }}"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Background Image <small class="text-muted">(optional)</small></label>
                                                                        @if($slide->bg_image)
                                                                            <div class="mb-2">
                                                                                <img src="{{ asset('storage/' . $slide->bg_image) }}" style="max-width:200px;max-height:100px;border-radius:6px;object-fit:cover;">
                                                                            </div>
                                                                        @endif
                                                                        <div class="custom-file">
                                                                            <input type="file" class="custom-file-input" name="bg_image" accept="image/*">
                                                                            <label class="custom-file-label">{{ $slide->bg_image ? 'Replace background' : 'Choose background' }}</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Feature Image <small class="text-muted">(optional)</small></label>
                                                                        @if($slide->feature_image)
                                                                            <div class="mb-2">
                                                                                <img src="{{ asset('storage/' . $slide->feature_image) }}" style="max-width:200px;max-height:100px;border-radius:6px;object-fit:cover;">
                                                                            </div>
                                                                        @endif
                                                                        <div class="custom-file">
                                                                            <input type="file" class="custom-file-input" name="feature_image" accept="image/*">
                                                                            <label class="custom-file-label">{{ $slide->feature_image ? 'Replace feature' : 'Choose feature' }}</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>Feature Image Position</label>
                                                                        <select name="image_position" class="form-control">
                                                                            <option value="right" {{ $slide->image_position === 'right' ? 'selected' : '' }}>Right Side</option>
                                                                            <option value="left" {{ $slide->image_position === 'left' ? 'selected' : '' }}>Left Side</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="fas fa-save"></i> Update
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No hero slides found. Create one!</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
});
</script>
@endpush
