<?php

namespace App\Actions\Product;

use App\Events\ProductUpdated;
use App\Models\Product;
use App\Services\FileUploadService;
use App\Services\ProductGalleryService;
use App\Services\ProductService;
use App\Services\ProductVariantService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;

class UpdateProductAction
{
    public function __construct(
        private ProductService $productService,
        private ProductVariantService $variantService,
        private ProductGalleryService $galleryService,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Execute the product update.
     */
    public function execute(Product $product, array $data): Product
    {
        // Store old attributes for event
        $oldAttributes = $product->getOriginal();

        // Handle thumbnail update
        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
            $this->fileUploadService->delete($product->thumbnail);
            $data['thumbnail'] = $this->fileUploadService->upload($data['thumbnail'], 'products/thumbnail');
        }

        // Auto-set product_type to 'variable' when variants are provided
        if (isset($data['variants']) && is_array($data['variants']) && count($data['variants']) > 0) {
            $data['product_type'] = 'variable';
        }

        // Update the product
        $product = $this->productService->update($product, $data);

        // Sync multiple categories (always include primary category)
        $categoryIds = isset($data['category_ids']) && is_array($data['category_ids'])
            ? $data['category_ids']
            : [];
        if ($product->category_id) {
            $categoryIds[] = $product->category_id;
        }
        $product->categories()->sync(array_unique(array_filter($categoryIds)));

        // Sync variants for variable products
        /*
        $indexToId = [];
        if ($product->product_type === 'variable') {
            if (isset($data['variants']) && is_array($data['variants'])) {
                $indexToId = $this->variantService->sync($product, $data['variants']);
            }
        } else {
            $this->variantService->deleteAll($product);
        }

        // Sync gallery images if provided
        if (isset($data['images']) && is_array($data['images'])) {
            $existingImageIds = $data['existing_images'] ?? [];
            $this->galleryService->sync($product, $data['images'], $existingImageIds);
        }

        // Delete marked images
        if (isset($data['deleted_images']) && is_array($data['deleted_images'])) {
            $this->deleteMarkedImages($product, $data['deleted_images']);
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
        */
        // Dispatch event
        Event::dispatch(new ProductUpdated($product, $oldAttributes));

        return $product;
    }

    /**
     * Delete marked gallery images.
     *
     * @param  array  $deletedImageIds  JSON string or array of image IDs
     */
    private function deleteMarkedImages(Product $product, $deletedImageIds): void
    {
        // Parse JSON string if needed
        if (is_string($deletedImageIds)) {
            $deletedImageIds = json_decode($deletedImageIds, true) ?? [];
        }

        if (empty($deletedImageIds)) {
            return;
        }

        foreach ($deletedImageIds as $imageId) {
            $image = $product->images()->find($imageId);

            if ($image) {
                // Delete file from storage
                $this->fileUploadService->delete($image->image);
                // Delete database record
                $image->delete();
            }
        }
    }
}
