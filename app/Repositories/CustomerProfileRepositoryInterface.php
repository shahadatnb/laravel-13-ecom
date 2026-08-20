<?php

namespace App\Repositories;

use App\Models\CustomerProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CustomerProfileRepositoryInterface
{
    public function find(int $id): ?CustomerProfile;

    public function findByUserId(int $userId): ?CustomerProfile;

    public function findByCustomerId(int $customerId): ?CustomerProfile;

    public function findByCustomerCode(string $customerCode): ?CustomerProfile;

    public function getAll(): Collection;

    public function create(array $data): CustomerProfile;

    public function update(CustomerProfile $customerProfile, array $data): CustomerProfile;

    public function delete(CustomerProfile $customerProfile): void;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function search(array $filters): Collection;

    public function count(): int;

    public function countActive(): int;

    public function countInactive(): int;

    public function countBanned(): int;
}
