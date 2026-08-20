@extends('admin.layouts.app')

@section('title', 'Media Library')

@push('styles')
<style>
.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
}
.media-item {
    border: 1px solid #d2d6de;
    border-radius: 6px;
    overflow: hidden;
    background: #fff;
    transition: box-shadow .2s;
}
.media-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,.12);
}
.media-thumb {
    width: 100%;
    height: 160px;
    object-fit: cover;
    background: #f4f4f4;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 40px;
}
.media-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.media-info {
    padding: 10px 12px;
}
.media-info .name {
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.media-info .meta {
    font-size: 11px;
    color: #888;
    margin-top: 4px;
}
.media-actions {
    display: flex;
    gap: 6px;
    padding: 8px 12px;
    border-top: 1px solid #f0f0f0;
}
.upload-zone {
    border: 2px dashed #d2d6de;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    background: #fafafa;
}
.upload-zone:hover,
.upload-zone.dragover {
    border-color: #3c8dbc;
    background: #f0f7fc;
}
.upload-zone i {
    font-size: 48px;
    color: #3c8dbc;
}
</style>
@endpush

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-photo-video"></i> Media Library</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadModal">
                                <i class="fas fa-upload"></i> Upload File
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search & Filter -->
                        <form method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Search files..." value="{{ request('search') }}">
                                        <span class="input-group-append">
                                            <button type="submit" class="btn btn-info">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="type" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Types</option>
                                        <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Images</option>
                                        <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Documents</option>
                                        <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Videos</option>
                                        <option value="audio" {{ request('type') == 'audio' ? 'selected' : '' }}>Audio</option>
                                    </select>
                                </div>
                                <div class="col-md-5 text-right">
                                    <a href="{{ route('admin.media.index') }}" class="btn btn-default">
                                        <i class="fas fa-sync"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>

                        @if($media->count())
                            <div class="media-grid">
                                @foreach($media as $item)
                                    <div class="media-item" data-id="{{ $item->id }}">
                                        <div class="media-thumb">
                                            @if($item->is_image)
                                                <img src="{{ $item->url }}" alt="{{ $item->alt_text ?: $item->name }}" loading="lazy">
                                            @else
                                                <i class="fas {{ $item->type === 'document' ? 'fa-file-alt' : ($item->type === 'video' ? 'fa-video' : 'fa-music') }}"></i>
                                            @endif
                                        </div>
                                        <div class="media-info">
                                            <div class="name" title="{{ $item->original_name }}">{{ $item->original_name }}</div>
                                            <div class="meta">
                                                {{ $item->formatted_size }}
                                                @if($item->created_at)
                                                    &middot; {{ $item->created_at->diffForHumans() }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="media-actions">
                                            <button type="button" class="btn btn-xs btn-info copy-url"
                                                data-url="{{ $item->url }}" title="Copy URL">
                                                <i class="fas fa-link"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-danger delete-media"
                                                data-id="{{ $item->id }}" data-name="{{ $item->original_name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                {{ $media->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-photo-video fa-4x text-muted mb-3"></i>
                                <p class="text-muted">No media files yet. Upload your first file!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload File</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="modal-body">
                    <div class="upload-zone" id="dropzone">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p class="mt-2 mb-1"><strong>Click or drag a file here</strong></p>
                        <p class="text-muted small">Max file size: 20MB</p>
                        <input type="file" name="file" id="fileInput" class="d-none" required>
                    </div>
                    <div id="filePreview" class="d-none mt-3">
                        <div class="alert alert-info mb-0" id="fileName"></div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="alt_text">Alt Text (for images)</label>
                        <input type="text" name="alt_text" id="alt_text" class="form-control" placeholder="Describe the image...">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" rows="2" class="form-control" placeholder="Optional description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">Delete File</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteFileName"></strong>?</p>
                <p class="text-danger small">This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form action="" method="POST" id="deleteForm">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    // Drag & drop upload zone
    const dropzone = $('#dropzone');
    const fileInput = $('#fileInput');
    const filePreview = $('#filePreview');
    const fileName = $('#fileName');
    const uploadBtn = $('#uploadBtn');

    dropzone.on('click', function() { fileInput.click(); });

    fileInput.on('change', function() {
        if (this.files && this.files[0]) {
            fileName.text('Selected: ' + this.files[0].name + ' (' + formatSize(this.files[0].size) + ')');
            filePreview.removeClass('d-none');
            uploadBtn.prop('disabled', false);
        }
    });

    // Drag events
    dropzone.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });
    dropzone.on('dragleave', function() {
        $(this).removeClass('dragover');
    });
    dropzone.on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        if (e.originalEvent.dataTransfer.files.length) {
            fileInput[0].files = e.originalEvent.dataTransfer.files;
            fileInput.trigger('change');
        }
    });

    // Copy URL
    $('.copy-url').on('click', function() {
        const url = $(this).data('url');
        navigator.clipboard.writeText(url).then(() => {
            $(this).tooltip({ title: 'Copied!', placement: 'top', trigger: 'manual' });
            $(this).tooltip('show');
            setTimeout(() => $(this).tooltip('hide'), 1500);
        });
    });

    // Delete confirmation
    var deleteRoute = '{{ route('admin.media.destroy', '__ID__') }}';
    $('.delete-media').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#deleteFileName').text(name);
        $('#deleteForm').attr('action', deleteRoute.replace('__ID__', id));
        $('#deleteModal').modal('show');
    });

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }
});
</script>
@endpush
