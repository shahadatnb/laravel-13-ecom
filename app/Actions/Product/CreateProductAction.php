<?php

namespace App\Actions\Product;

use App\Events\ProductCreated;
use App\Models\Product;
use App\Services\FileUploadService;
use App\Services\ProductGalleryService;
use App\Services\ProductService;
use App\Services\ProductVariantService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;

class CreateProductAction
{
    public function __construct(
        private ProductService $productService,
        private ProductVariantService $variantService,
        private ProductGalleryService $galleryService,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Execute the product creation.
     */
    public function execute(array $data): Product
    {
        // Handle thumbnail upload
        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
            $data['thumbnail'] = $this->fileUploadService->upload($data['thumbnail'], 'products/thumbnail');
        }

        // Auto-set product_type to 'variable' when variants are provided
        if (isset($data['variants']) && is_array($data['variants']) && count($data['variants']) > 0) {
            $data['product_type'] = 'variable';
        }

        // Create the product
        $product = $this->productService->create($data);

        // Sync multiple categories (always include primary category)
        $categoryIds = isset($data['category_ids']) && is_array($data['category_ids'])
            ? $data['category_ids']
            : [];
        if ($product->category_id) {
            $categoryIds[] = $product->category_id;
        }
        $product->categories()->sync(array_unique(array_filter($categoryIds)));

        // Sync variants if provided
        $indexToId = [];
        if (isset($data['variants']) && is_array($data['variants'])) {
            $indexToId = $this->variantService->sync($product, $data['variants']);
        }

        // Sync gallery images if provided
        if (isset($data['images']) && is_array($data['images'])) {
            $existingImageIds = $data['existing_images'] ?? [];
            $this->galleryService->sync($product, $data['images'], $existingImageIds);
        }

        // Sync variant images if provided — remap temporary indices to real variant IDs
        if (isset($data['variant_images']) && is_array($data['variant_images'])) {
            $existingVariantImages = $data['variant_existing_images'] ?? [];

            // Remap temporary index keys to real variant IDs
            $remappedImages = [];
            foreach ($data['variant_images'] as $oldKey => $images) {
                $newKey = $indexToId[(string) $oldKey] ?? $oldKey;
                $remappedImages[$newKey] = $images;
            }

            $remappedExisting = [];
            foreach ($existingVariantImages as $oldKey => $ids) {
                $newKey = $indexToId[(string) $oldKey] ?? $oldKey;
                $remappedExisting[$newKey] = $ids;
            }

            $this->galleryService->syncVariantImages($product, $remappedImages, $remappedExisting);
        }

        // Dispatch event
        Event::dispatch(new ProductCreated($product));

        return $product;
    }
}
