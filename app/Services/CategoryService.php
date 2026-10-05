<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(private CategoryRepository $categoryRepository) {}

    /**
     * List categories, optionally filtered.
     *
     * @param  array{q?: string, status?: string, featured?: string, parent_id?: int|string}  $filters
     */
    public function list(array $filters = []): Collection
    {
        return $this->categoryRepository->getFiltered($filters);
    }

    public function tree(): Collection
    {
        return $this->categoryRepository->getTree();
    }

    public function find(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    public function create(array $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        return $this->categoryRepository->update($category, $data);
    }

    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }
}
