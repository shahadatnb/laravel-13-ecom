<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    /**
     * Get all products.
     */
    public function list(): Collection
    {
        return $this->productRepository->getAll();
    }

    /**
     * Get low stock products (stock <= minimum_stock).
     */
    public function getLowStock(): Collection
    {
        return $this->productRepository->getLowStock();
    }

    /**
     * Get out of stock products (stock <= 0).
     */
    public function getOutOfStock(): Collection
    {
        return $this->productRepository->getOutOfStock();
    }

    /**
     * Get active products.
     */
    public function getActive(): Collection
    {
        return $this->productRepository->getActive();
    }

    /**
     * Get featured products.
     */
    public function getFeatured(): Collection
    {
        return $this->productRepository->getFeatured();
    }

    /**
     * Find a product by ID.
     */
    public function find(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    /**
     * Create a new product.
     */
    public function create(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    /**
     * Update an existing product.
     */
    public function update(Product $product, array $data): Product
    {
        return $this->productRepository->update($product, $data);
    }

    /**
     * Delete a product.
     */
    public function delete(Product $product): void
    {
        $this->productRepository->delete($product);
    }

    /**
     * Get all products paginated.
     */
    public function listPaginated(int $perPage = 50)
    {
        return $this->productRepository->getAllPaginated($perPage);
    }

    /**
     * Get low stock products paginated.
     */
    public function getLowStockPaginated(int $perPage = 50)
    {
        return $this->productRepository->getLowStockPaginated($perPage);
    }

    /**
     * Get in stock products paginated.
     */
    public function getInStockPaginated(int $perPage = 50)
    {
        return $this->productRepository->getInStockPaginated($perPage);
    }

    /**
     * Get out of stock products paginated.
     */
    public function getOutOfStockPaginated(int $perPage = 50)
    {
        return $this->productRepository->getOutOfStockPaginated($perPage);
    }

    /**
     * Search products by name or SKU.
     */
    public function search(string $query, int $limit = 10): Collection
    {
        return $this->productRepository->search($query, $limit);
    }

    /**
     * Get products by category.
     */
    public function getByCategory(int $categoryId): Collection
    {
        return $this->productRepository->getByCategory($categoryId);
    }

    /**
     * Get products by brand.
     */
    public function getByBrand(int $brandId): Collection
    {
        return $this->productRepository->getByBrand($brandId);
    }

    /**
     * Filter products with advanced filters.
     */
    public function filter(array $filters): Collection
    {
        return $this->productRepository->filter($filters);
    }

    /**
     * Get product statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->productRepository->count(),
            'active' => $this->productRepository->countActive(),
            'featured' => $this->productRepository->countFeatured(),
            'out_of_stock' => $this->productRepository->countOutOfStock(),
        ];
    }
}
