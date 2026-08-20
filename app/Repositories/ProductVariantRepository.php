<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;

class ProductVariantRepository
{
    /**
     * Find variant by ID.
     */
    public function find(int $id): ?ProductVariant
    {
        return ProductVariant::find($id);
    }

    /**
     * Create a new variant for a product.
     */
    public function create(Product $product, array $data): ProductVariant
    {
        return $product->variants()->create($data);
    }

    /**
     * Update a variant.
     */
    public function update(ProductVariant $variant, array $data): ProductVariant
    {
        $variant->update($data);

        return $variant->fresh();
    }

    /**
     * Delete a variant.
     */
    public function delete(ProductVariant $variant): void
    {
        $variant->delete();
    }

    /**
     * Get variants by product ID.
     */
    public function getByProductId(int $productId): Collection
    {
        return ProductVariant::where('product_id', $productId)
            ->orderBy('sort_order')
            ->get();
    }
}
