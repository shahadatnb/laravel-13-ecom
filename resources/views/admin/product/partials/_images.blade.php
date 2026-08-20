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
</div>