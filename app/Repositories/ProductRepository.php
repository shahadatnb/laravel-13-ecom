<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    /**
     * Get all products with relationships.
     */
    public function getAll(): Collection
    {
        return Product::with(['brand', 'category'])->orderByDesc('id')->get();
    }

    /**
     * Get active products.
     */
    public function getActive(): Collection
    {
        return Product::where('status', 'published')->orderByDesc('id')->get();
    }

    /**
     * Get featured products.
     */
    public function getFeatured(): Collection
    {
        return Product::where('featured', true)
            ->where('status', 'published')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Find a product by ID.
     */
    public function find(int $id): ?Product
    {
        return Product::with(['variants.images', 'brand', 'category'])->find($id);
    }

    /**
     * Create a new product.
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update an existing product.
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh();
    }

    /**
     * Delete a product.
     */
    public function delete(Product $product): void
    {
        $product->delete();
    }

    /**
     * Search products by name or SKU.
     */
    public function search(string $query, int $limit = 10): Collection
    {
        return Product::with(['brand', 'category'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Get products by category.
     */
    public function getByCategory(int $categoryId): Collection
    {
        return Product::with(['brand', 'category'])
            ->where('category_id', $categoryId)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Get products by brand.
     */
    public function getByBrand(int $brandId): Collection
    {
        return Product::with(['brand', 'category'])
            ->where('brand_id', $brandId)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Filter products with advanced filters.
     */
    public function filter(array $filters): Collection
    {
        $query = Product::with(['brand', 'category']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['product_type'])) {
            $query->where('product_type', $filters['product_type']);
        }

        if (isset($filters['featured'])) {
            $query->where('featured', $filters['featured']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['min_price']) && isset($filters['max_price'])) {
            $query->whereBetween('regular_price', [$filters['min_price'], $filters['max_price']]);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('sku', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['sort'])) {
            $direction = $filters['sort_direction'] ?? 'desc';
            $query->orderBy($filters['sort'], $direction);
        }

        return $query->get();
    }

    /**
     * Count total products.
     */
    public function count(): int
    {
        return Product::count();
    }

    /**
     * Count active products.
     */
    public function countActive(): int
    {
        return Product::where('status', 'published')->count();
    }

    /**
     * Count featured products.
     */
    public function countFeatured(): int
    {
        return Product::where('featured', true)->count();
    }

    /**
     * Get low stock products (stock <= minimum_stock).
     */
    public function getLowStock(): Collection
    {
        return Product::with(['brand', 'category'])
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->where('stock', '>', 0)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Get out of stock products (stock <= 0).
     */
    public function getOutOfStock(): Collection
    {
        return Product::with(['brand', 'category'])
            ->where('stock', '<=', 0)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Count out of stock products.
     */
    public function countOutOfStock(): int
    {
        return Product::where('stock', 0)->count();
    }

    /**
     * Get all products paginated.
     *
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 50)
    {
        return Product::with(['brand', 'category', 'variants'])->orderByDesc('id')->paginate($perPage);
    }

    /**
     * Get in stock products paginated (stock > minimum_stock).
     *
     * @return LengthAwarePaginator
     */
    public function getInStockPaginated(int $perPage = 50)
    {
        return Product::with(['brand', 'category', 'variants'])
            ->whereColumn('stock', '>', 'minimum_stock')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Get low stock products paginated.
     *
     * @return LengthAwarePaginator
     */
    public function getLowStockPaginated(int $perPage = 50)
    {
        return Product::with(['brand', 'category', 'variants'])
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->where('stock', '>', 0)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Get out of stock products paginated.
     *
     * @return LengthAwarePaginator
     */
    public function getOutOfStockPaginated(int $perPage = 50)
    {
        return Product::with(['brand', 'category', 'variants'])
            ->where('stock', '<=', 0)
            ->orderByDesc('id')
            ->paginate($perPage);
    }
}
