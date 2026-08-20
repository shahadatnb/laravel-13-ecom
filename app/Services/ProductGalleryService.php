<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\UploadedFile;

class ProductGalleryService
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Sync product gallery images.
     */
    public function sync(Product $product, array $newImages, array $existingImageIds = []): void
    {
        $existing = $product->images;

        foreach ($existing as $image) {
            if (in_array($image->id, $existingImageIds, true)) {
                continue;
            }

            $this->fileUploadService->delete($image->image);
            $image->delete();
        }

        foreach ($newImages as $image) {
            if (! $image instanceof UploadedFile) {
                continue;
            }

            $path = $this->fileUploadService->upload($image, 'products/gallery');

            $product->images()->create([
                'image' => $path,
                'alt_text' => '',
                'caption' => '',
                'sort_order' => 0,
            ]);
        }
    }

    /**
     * Sync variant images.
     */
    public function syncVariantImages(Product $product, array $variantImages, array $existingVariantImages = []): void
    {
        foreach ($variantImages as $variantId => $images) {
            $variant = $product->variants()->find($variantId);

            if (! $variant) {
                continue;
            }

            $this->syncVariant($variant, $images, $existingVariantImages[$variantId] ?? []);
        }
    }

    /**
     * Sync images for a single variant.
     */
    public function syncVariant(ProductVariant $variant, array $newImages, array $existingImageIds = []): void
    {
        $existing = $variant->images;

        foreach ($existing as $image) {
            if (in_array($image->id, $existingImageIds, true)) {
                continue;
            }

            $this->fileUploadService->delete($image->image);
            $image->delete();
        }

        foreach ($newImages as $image) {
            if (! $image instanceof UploadedFile) {
                continue;
            }

            $path = $this->fileUploadService->upload($image, 'products/gallery');

            $variant->images()->create([
                'product_id' => $variant->product_id,
                'image' => $path,
                'alt_text' => '',
                'caption' => '',
                'sort_order' => 0,
            ]);
        }
    }

    /**
     * Delete all images for a variant.
     */
    public function deleteAllForVariant(ProductVariant $variant): void
    {
        $variant->images()->each(function ($image) {
            $this->fileUploadService->delete($image->image);
            $image->delete();
        });
    }

    /**
     * Delete all images for a product.
     */
    public function deleteAllForProduct(Product $product): void
    {
        $product->images()->each(function ($image) {
            $this->fileUploadService->delete($image->image);
            $image->delete();
        });
    }

    /**
     * Add a single image to product gallery.
     */
    public function addImage(Product $product, UploadedFile $file): ?string
    {
        $path = $this->fileUploadService->upload($file, 'products/gallery');

        if ($path) {
            $product->images()->create([
                'image' => $path,
                'alt_text' => '',
                'caption' => '',
                'sort_order' => 0,
            ]);
        }

        return $path;
    }

    /**
     * Add a single image to variant gallery.
     */
    public function addVariantImage(ProductVariant $variant, UploadedFile $file): ?string
    {
        $path = $this->fileUploadService->upload($file, 'products/gallery');

        if ($path) {
            $variant->images()->create([
                'product_id' => $variant->product_id,
                'image' => $path,
                'alt_text' => '',
                'caption' => '',
                'sort_order' => 0,
            ]);
        }

        return $path;
    }
}
