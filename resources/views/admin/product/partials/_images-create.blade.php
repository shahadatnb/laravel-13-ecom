<div id="section-images">
<div class="card card-secondary">
    <div class="card-header bg-gradient bg-primary">
        <h3 class="card-title">
            <i class="fas fa-images mr-2"></i>Product Images
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
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
                            <div class="thumbnail-placeholder">
                                <i class="fas fa-image fa-3x text-muted"></i>
                                <p class="mt-2 mb-0 text-muted">No thumbnail selected</p>
                            </div>
                        </div>
                        <input id="thumbnail" name="thumbnail" type="file" class="d-none" accept="image/*" />
                        <button type="button" class="btn btn-primary btn-block thumbnail-upload-btn" id="thumbnail-upload-btn">
                            <i class="fas fa-upload"></i> Upload Thumbnail
                        </button>
                    </div>
                    @error('thumbnail')<span class="text-danger small mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-weight-bold">
                        <i class="fas fa-images text-success"></i> Product Gallery
                    </label>
                    <p class="text-muted small mb-2">
                        <i class="fas fa-info-circle"></i> Drag & drop or click to upload (Max 10 images)
                    </p>
                    
                    <div class="gallery-drop-zone" id="gallery-drop-zone">
                        <div class="drop-zone-content">
                            <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                            <p class="mt-2 mb-1"><strong>Drag & Drop</strong> images here</p>
                            <p class="text-muted small mb-0">or click to browse</p>
                            <span class="badge badge-info mt-2">Supports: JPG, PNG, WEBP</span>
                        </div>
                        <input id="gallery-input" name="images[]" type="file" class="d-none" accept="image/*" multiple />
                    </div>

                    <div class="new-gallery-preview mt-3" id="new-gallery-preview"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-light">
        <small class="text-muted">
            <i class="fas fa-lightbulb text-warning"></i> 
            Tip: Upload high-quality images for better customer experience. Images will be saved when you submit the form.
        </small>
    </div>
</div>
</div>