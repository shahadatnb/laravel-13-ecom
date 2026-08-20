<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\ProductVariantRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductVariantService
{
    public function __construct(
        private ProductVariantRepository $variantRepository,
        private ProductGalleryService $galleryService
    ) {}

    /**
     * Sync product variants.
     *
     * @return array<string, int> Mapping of old input index → new variant ID
     */
    /**
     * Normalize variant attributes to an array.
     *
     * The admin form sends `attributes` as a JSON string via hidden input.
     * When passed to the model's `array` cast, it gets double-encoded.
     * This helper decodes the string to a proper array.
     */
    private function normalizeAttributes(mixed $attributes): array
    {
        if (is_array($attributes)) {
            return $attributes;
        }

        if (is_string($attributes) && $attributes !== '') {
            $decoded = json_decode($attributes, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    public function sync(Product $product, array $variants): array
    {
        $existing = $product->variants;
        $keepIds = [];
        $indexToId = []; // old index → new variant ID mapping

        foreach ($variants as $index => $variantData) {
            if (empty($variantData['name'])) {
                continue;
            }

            $attrs = $this->normalizeAttributes($variantData['attributes'] ?? []);

            if (isset($variantData['id']) && ($existingVariant = $existing->where('id', $variantData['id'])->first())) {
                // Update existing variant data (SKU, price, stock, etc.)
                $existingVariant->update([
                    'name' => $variantData['name'] ?? $existingVariant->name,
                    'sku' => $variantData['sku'] ?? $existingVariant->sku,
                    'barcode' => $variantData['barcode'] ?? $existingVariant->barcode,
                    'price' => array_key_exists('price', $variantData) ? $variantData['price'] : $existingVariant->price,
                    'stock' => array_key_exists('stock', $variantData) ? $variantData['stock'] : $existingVariant->stock,
                    'attributes' => $attrs,
                    'sort_order' => $index,
                ]);

                $keepIds[] = $variantData['id'];
                $indexToId[(string) $index] = $variantData['id'];

                continue;
            }

            $variant = $product->variants()->create([
                'name' => $variantData['name'],
                'sku' => $variantData['sku'] ?? null,
                'barcode' => $variantData['barcode'] ?? null,
                'price' => $variantData['price'] ?? null,
                'stock' => $variantData['stock'] ?? 0,
                'attributes' => $attrs,
                'sort_order' => $index,
            ]);

            $keepIds[] = $variant->id;
            $indexToId[(string) $index] = $variant->id;
        }

        $this->removeOrphanVariants($product, $existing, $keepIds);

        return $indexToId;
    }

    /**
     * Remove orphan variants and their images.
     */
    protected function removeOrphanVariants(Product $product, Collection $existing, array $keepIds): void
    {
        $existing->each(function ($variant) use ($keepIds) {
            if (! in_array($variant->id, $keepIds)) {
                $this->galleryService->deleteAllForVariant($variant);
                $variant->delete();
            }
        });
    }

    /**
     * Get variant by ID.
     */
    public function find(int $id): ?ProductVariant
    {
        return $this->variantRepository->find($id);
    }

    /**
     * Create a new variant.
     */
    public function create(Product $product, array $data): ProductVariant
    {
        return $this->variantRepository->create($product, $data);
    }

    /**
     * Update a variant.
     */
    public function update(ProductVariant $variant, array $data): ProductVariant
    {
        return $this->variantRepository->update($variant, $data);
    }

    /**
     * Delete a variant.
     */
    public function delete(ProductVariant $variant): void
    {
        $this->galleryService->deleteAllForVariant($variant);
        $this->variantRepository->delete($variant);
    }

    /**
     * Delete all variants for a product.
     */
    public function deleteAll(Product $product): void
    {
        $product->variants()->each(function ($variant) {
            $this->galleryService->deleteAllForVariant($variant);
            $variant->delete();
        });
    }
}
