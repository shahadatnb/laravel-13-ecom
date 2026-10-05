<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    public function getAll()
    {
        return Category::with('parent')->orderBy('sort_order')->orderByDesc('id')->get();
    }

    /**
     * Get categories filtered by search, status, featured and parent category.
     *
     * @param  array{q?: string, status?: string, featured?: string, parent_id?: int|string}  $filters
     * @return Collection
     */
    public function getFiltered(array $filters = [])
    {
        return Category::with('parent')
            ->when($filters['q'] ?? '', function ($query, $search) {
                $search = '%'.trim($search).'%';

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', $search)
                        ->orWhere('name_bn', 'like', $search)
                        ->orWhere('slug', 'like', $search);
                });
            })
            ->when($filters['status'] ?? '', fn ($query, $status) => $query->where('status', $status))
            ->when(($filters['featured'] ?? '') !== '', fn ($query) => $query->where('featured', $filters['featured'] === '1'))
            ->when($filters['parent_id'] ?? '', fn ($query, $parentId) => $query->where('parent_id', $parentId))
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();
    }

    public function getTree()
    {
        return Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
    }

    public function find(int $id): ?Category
    {
        return Category::find($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->fresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }

    public function getActive()
    {
        return Category::where('status', 'active')->orderBy('sort_order')->get();
    }

    public function getFeatured()
    {
        return Category::where('featured', true)->where('status', 'active')->orderBy('sort_order')->get();
    }
}
