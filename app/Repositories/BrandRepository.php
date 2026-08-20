<?php

namespace App\Repositories;

use App\Models\Brand;

class BrandRepository
{
    public function getAll()
    {
        return Brand::orderBy('sort_order')->orderByDesc('id')->get();
    }

    public function find(int $id): ?Brand
    {
        return Brand::find($id);
    }

    public function create(array $data): Brand
    {
        return Brand::create($data);
    }

    public function update(Brand $brand, array $data): Brand
    {
        $brand->update($data);

        return $brand->fresh();
    }

    public function delete(Brand $brand): void
    {
        $brand->delete();
    }

    public function getActive()
    {
        return Brand::where('status', 'active')->orderBy('sort_order')->get();
    }

    public function getFeatured()
    {
        return Brand::where('featured', true)->where('status', 'active')->orderBy('sort_order')->get();
    }
}
