<!-- Browse Media Modal — reusable for Editor.js pages -->
<div class="modal fade" id="browseMediaModal" tabindex="-1" role="dialog" aria-labelledby="browseMediaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="browseMediaLabel">
                    <i class="fas fa-photo-video"></i> Browse Media Library
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Search -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" id="browseSearch" class="form-control" placeholder="Search files...">
                            <span class="input-group-append">
                                <button class="btn btn-info" type="button" id="browseSearchBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="browseTypeFilter" class="form-control">
                            <option value="image">Images</option>
                            <option value="document">Documents</option>
                            <option value="video">Videos</option>
                            <option value="audio">Audio</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#browseUploadModal">
                            <i class="fas fa-upload"></i> Upload New
                        </button>
                    </div>
                </div>

                <!-- Grid -->
                <div id="browseGrid" class="row">
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-3x text-muted"></i>
                        <p class="text-muted mt-2">Loading media...</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div id="browsePagination" class="text-center mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Upload Modal (inside Browse Media) -->
<div class="modal fade" id="browseUploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload File</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="upload-zone" id="browseDropzone" style="border:2px dashed #d2d6de;border-radius:8px;padding:30px 20px;text-align:center;cursor:pointer;background:#fafafa;">
                    <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                    <p class="mt-2 mb-1"><strong>Click or drag a file here</strong></p>
                    <p class="text-muted small">Max file size: 20MB</p>
                    <input type="file" name="file" id="browseFileInput" class="d-none" accept="image/*" required>
                </div>
                <div id="browseFilePreview" class="d-none mt-2 alert alert-info mb-0"></div>
                <div id="browseUploadProgress" class="d-none mt-2">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width:0%"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="browseUploadBtn" disabled>
                    <i class="fas fa-upload"></i> Upload
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.browse-media-item {
    border: 2px solid transparent;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all .2s;
    background: #fff;
}
.browse-media-item:hover {
    border-color: #3c8dbc;
    box-shadow: 0 2px 8px rgba(60,141,188,.3);
}
.browse-media-item.selected {
    border-color: #00a65a;
    box-shadow: 0 2px 8px rgba(0,166,90,.3);
}
.browse-media-thumb {
    width: 100%;
    height: 140px;
    object-fit: cover;
    background: #f4f4f4;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 36px;
}
.browse-media-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.browse-media-info {
    padding: 8px 10px;
}
.browse-media-info .name {
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.browse-media-info .meta {
    font-size: 10px;
    color: #888;
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    // Use IIFE to isolate scope — no global variables to conflict
    var browseCurrentPage = 1;
    var browseSelectedUrl = null;

    function loadBrowseMedia(page, search, type) {
        document.getElementById('browseGrid').innerHTML =
            '<div class="col-12 text-center py-5">' +
            '<i class="fas fa-spinner fa-spin fa-3x text-muted"></i>' +
            '<p class="text-muted mt-2">Loading media...</p></div>';

        var params = 'page=' + page + '&per_page=24';
        if (search) params += '&search=' + encodeURIComponent(search);
        if (type) params += '&type=' + encodeURIComponent(type);

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '{{ route("admin.media.browse") }}?' + params, true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var res = JSON.parse(xhr.responseText);
                    renderBrowseGrid(res);
                } catch(e) {
                    showBrowseError();
                }
            } else {
                showBrowseError();
            }
        };
        xhr.onerror = showBrowseError;
        xhr.send();
    }

    function renderBrowseGrid(res) {
        if (res.success && res.data.length) {
            var html = '';
            for (var i = 0; i < res.data.length; i++) {
                var item = res.data[i];
                var isImage = item.type === 'image';
                var escapedName = item.original_name.replace(/'/g, "\\'");
                html += '<div class="col-6 col-md-4 col-lg-3 mb-3">' +
                    '<div class="browse-media-item" data-url="' + item.url + '">' +
                    '<div class="browse-media-thumb">' +
                    (isImage ? '<img src="' + item.url + '" alt="' + escapedName + '" loading="lazy">' : '<i class="fas fa-file-alt"></i>') +
                    '</div>' +
                    '<div class="browse-media-info">' +
                    '<div class="name" title="' + escapedName + '">' + escapedName + '</div>' +
                    '<div class="meta">' + item.size + '</div>' +
                    '</div></div></div>';
            }
            document.getElementById('browseGrid').innerHTML = html;

            // Pagination
            var pagHtml = '';
            if (res.pagination.last_page > 1) {
                pagHtml += '<nav><ul class="pagination pagination-sm justify-content-center">';
                for (var p = 1; p <= res.pagination.last_page; p++) {
                    pagHtml += '<li class="page-item ' + (p === res.pagination.current_page ? 'active' : '') + '">' +
                        '<a class="page-link" href="#" data-page="' + p + '">' + p + '</a></li>';
                }
                pagHtml += '</ul></nav>';
            }
            document.getElementById('browsePagination').innerHTML = pagHtml;

            // Click handlers using event delegation (no jQuery)
            document.querySelectorAll('.browse-media-item').forEach(function(el) {
                el.onclick = function() {
                    document.querySelectorAll('.browse-media-item').forEach(function(e) {
                        e.classList.remove('selected');
                    });
                    this.classList.add('selected');
                    browseSelectedUrl = this.getAttribute('data-url');
                    if (window.onBrowseMediaSelect) {
                        window.onBrowseMediaSelect(browseSelectedUrl);
                        $('#browseMediaModal').modal('hide');
                    }
                };
            });

            document.querySelectorAll('.page-link').forEach(function(el) {
                el.onclick = function(e) {
                    e.preventDefault();
                    var p = parseInt(this.getAttribute('data-page'));
                    if (p !== browseCurrentPage) {
                        browseCurrentPage = p;
                        loadBrowseMedia(p, document.getElementById('browseSearch').value,
                            document.getElementById('browseTypeFilter').value);
                    }
                };
            });
        } else {
            document.getElementById('browseGrid').innerHTML =
                '<div class="col-12 text-center py-5">' +
                '<i class="fas fa-photo-video fa-4x text-muted mb-3"></i>' +
                '<p class="text-muted">No media found. Upload some files first!</p></div>';
            document.getElementById('browsePagination').innerHTML = '';
        }
    }

    function showBrowseError() {
        document.getElementById('browseGrid').innerHTML =
            '<div class="col-12 text-center py-5 text-danger">' +
            '<i class="fas fa-exclamation-triangle fa-3x mb-3"></i>' +
            '<p>Failed to load media.</p></div>';
    }

    // Bind events on DOM ready using jQuery (since AdminLTE has jQuery)
    $(function() {
        // Open browse modal — load media
        $('#browseMediaModal').on('show.bs.modal', function() {
            browseCurrentPage = 1;
            browseSelectedUrl = null;
            loadBrowseMedia(1, '', 'image');
        });

        // When modal closes without selection, clear any pending Media & Text callback
        $('#browseMediaModal').on('hidden.bs.modal', function() {
            window._mediaTextCallback = null;
        });

        // Search
        $('#browseSearchBtn').on('click', function() {
            browseCurrentPage = 1;
            loadBrowseMedia(1, $('#browseSearch').val(), $('#browseTypeFilter').val());
        });
        $('#browseSearch').on('keydown', function(e) {
            if (e.key === 'Enter') {
                browseCurrentPage = 1;
                loadBrowseMedia(1, $(this).val(), $('#browseTypeFilter').val());
            }
        });
        $('#browseTypeFilter').on('change', function() {
            browseCurrentPage = 1;
            loadBrowseMedia(1, $('#browseSearch').val(), $(this).val());
        });

        // Quick upload inside browse modal — use vanilla JS to avoid jQuery conflict
        var bDropzone = document.getElementById('browseDropzone');
        var bFileInput = document.getElementById('browseFileInput');
        var bPreview = document.getElementById('browseFilePreview');
        var bUploadBtn = document.getElementById('browseUploadBtn');
        var bProgress = document.getElementById('browseUploadProgress');
        var bProgressBar = bProgress ? bProgress.querySelector('.progress-bar') : null;

        if (bDropzone) {
            bDropzone.onclick = function() { bFileInput.click(); };
            bFileInput.onchange = function() {
                if (this.files && this.files[0]) {
                    bPreview.textContent = 'Selected: ' + this.files[0].name;
                    bPreview.classList.remove('d-none');
                    bUploadBtn.disabled = false;
                }
            };
            bDropzone.ondragover = function(e) {
                e.preventDefault();
                this.style.borderColor = '#3c8dbc';
                this.style.background = '#f0f7fc';
            };
            bDropzone.ondragleave = function() {
                this.style.borderColor = '#d2d6de';
                this.style.background = '#fafafa';
            };
            bDropzone.ondrop = function(e) {
                e.preventDefault();
                this.style.borderColor = '#d2d6de';
                this.style.background = '#fafafa';
                if (e.dataTransfer.files.length) {
                    bFileInput.files = e.dataTransfer.files;
                    bFileInput.onchange();
                }
            };
        }

        if (bUploadBtn) {
            bUploadBtn.onclick = function() {
                var file = bFileInput.files[0];
                if (!file) return;

                var formData = new FormData();
                formData.append('file', file);

                bUploadBtn.disabled = true;
                bUploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
                bProgress.classList.remove('d-none');
                if (bProgressBar) bProgressBar.style.width = '0%';

                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("admin.media.upload-ajax") }}', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable && bProgressBar) {
                        bProgressBar.style.width = Math.round((e.loaded / e.total) * 100) + '%';
                    }
                };
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        $('#browseUploadModal').modal('hide');
                        loadBrowseMedia(browseCurrentPage,
                            document.getElementById('browseSearch').value,
                            document.getElementById('browseTypeFilter').value);
                        bFileInput.value = '';
                        bPreview.classList.add('d-none');
                        bUploadBtn.disabled = true;
                        bUploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload';
                        bProgress.classList.add('d-none');
                    } else {
                        alert('Upload failed.');
                        bUploadBtn.disabled = false;
                        bUploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload';
                        bProgress.classList.add('d-none');
                    }
                };
                xhr.onerror = function() {
                    alert('Upload failed.');
                    bUploadBtn.disabled = false;
                    bUploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload';
                    bProgress.classList.add('d-none');
                };
                xhr.send(formData);
            };
        }

        // Reset upload modal on close
        $('#browseUploadModal').on('hidden.bs.modal', function() {
            bFileInput.value = '';
            bPreview.classList.add('d-none');
            bUploadBtn.disabled = true;
            bUploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload';
            bProgress.classList.add('d-none');
            if (bProgressBar) bProgressBar.style.width = '0%';
        });
    });
})();
</script>
@endpush
