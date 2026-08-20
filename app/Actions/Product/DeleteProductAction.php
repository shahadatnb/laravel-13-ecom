<?php

namespace App\Actions\Product;

use App\Events\ProductDeleted;
use App\Models\Product;
use App\Services\FileUploadService;
use App\Services\ProductGalleryService;
use App\Services\ProductService;
use App\Services\ProductVariantService;
use Illuminate\Support\Facades\Event;

class DeleteProductAction
{
    public function __construct(
        private ProductService $productService,
        private ProductVariantService $variantService,
        private ProductGalleryService $galleryService,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Execute the product deletion.
     */
    public function execute(Product $product): void
    {
        // Dispatch event before deletion
        Event::dispatch(new ProductDeleted($product));

        // Delete thumbnail
        $this->fileUploadService->delete($product->thumbnail);

        // Delete gallery images
        $this->galleryService->deleteAllForProduct($product);

        // Delete variant images and variants
        $this->variantService->deleteAll($product);

        // Delete the product
        $this->productService->delete($product);
    }
}
