<?php

namespace App\Services;

use App\DTO\CreateCustomerProfileDTO;
use App\Models\CustomerProfile;
use App\Repositories\CustomerProfileRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerService implements CustomerServiceInterface
{
    public function __construct(
        private readonly CustomerProfileRepositoryInterface $customerRepository,
    ) {}

    public function getCustomerProfile(int $customerId): ?CustomerProfile
    {
        return $this->customerRepository->findByCustomerId($customerId);
    }

    public function createCustomerProfile(CreateCustomerProfileDTO $dto): CustomerProfile
    {
        return DB::transaction(function () use ($dto) {
            $data = $dto->toArray();

            return $this->customerRepository->create($data);
        });
    }

    public function updateCustomerProfile(CustomerProfile $customerProfile, array $data): CustomerProfile
    {
        return DB::transaction(function () use ($customerProfile, $data) {
            return $this->customerRepository->update($customerProfile, $data);
        });
    }

    public function deleteCustomerProfile(CustomerProfile $customerProfile): void
    {
        DB::transaction(function () use ($customerProfile) {
            $this->customerRepository->delete($customerProfile);
        });
    }

    public function listCustomers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->customerRepository->paginate($perPage);
    }

    public function searchCustomers(array $filters): Collection
    {
        return $this->customerRepository->search($filters);
    }

    public function getCustomerStatistics(): array
    {
        return [
            'total' => $this->customerRepository->count(),
            'active' => $this->customerRepository->countActive(),
            'inactive' => $this->customerRepository->countInactive(),
            'banned' => $this->customerRepository->countBanned(),
        ];
    }

    public function updateCustomerStatus(CustomerProfile $customerProfile, string $status): CustomerProfile
    {
        return $this->updateCustomerProfile($customerProfile, ['status' => $status]);
    }
}
