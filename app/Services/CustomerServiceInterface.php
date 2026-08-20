<?php

namespace App\Services;

use App\DTO\CreateCustomerProfileDTO;
use App\Models\CustomerProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CustomerServiceInterface
{
    public function getCustomerProfile(int $customerId): ?CustomerProfile;

    public function createCustomerProfile(CreateCustomerProfileDTO $dto): CustomerProfile;

    public function updateCustomerProfile(CustomerProfile $customerProfile, array $data): CustomerProfile;

    public function deleteCustomerProfile(CustomerProfile $customerProfile): void;

    public function listCustomers(int $perPage = 15): LengthAwarePaginator;

    public function searchCustomers(array $filters): Collection;

    public function getCustomerStatistics(): array;

    public function updateCustomerStatus(CustomerProfile $customerProfile, string $status): CustomerProfile;
}
