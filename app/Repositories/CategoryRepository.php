<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function getAll()
    {
        return Category::with('parent')->orderBy('sort_order')->orderByDesc('id')->get();
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
