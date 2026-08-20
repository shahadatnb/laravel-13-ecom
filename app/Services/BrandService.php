<?php

namespace App\Services;

use App\Models\Brand;
use App\Repositories\BrandRepository;
use Illuminate\Database\Eloquent\Collection;

class BrandService
{
    public function __construct(private BrandRepository $brandRepository) {}

    public function list(): Collection
    {
        return $this->brandRepository->getAll();
    }

    public function find(int $id): ?Brand
    {
        return $this->brandRepository->find($id);
    }

    public function create(array $data): Brand
    {
        return $this->brandRepository->create($data);
    }

    public function update(Brand $brand, array $data): Brand
    {
        return $this->brandRepository->update($brand, $data);
    }

    public function delete(Brand $brand): void
    {
        $this->brandRepository->delete($brand);
    }
}
