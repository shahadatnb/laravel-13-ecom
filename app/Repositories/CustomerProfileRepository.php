<?php

namespace App\Repositories;

use App\Models\CustomerProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerProfileRepository implements CustomerProfileRepositoryInterface
{
    public function find(int $id): ?CustomerProfile
    {
        return CustomerProfile::with(['customer', 'user'])->find($id);
    }

    public function findByUserId(int $userId): ?CustomerProfile
    {
        return CustomerProfile::with(['customer', 'user'])
            ->where('user_id', $userId)
            ->first();
    }

    public function findByCustomerId(int $customerId): ?CustomerProfile
    {
        return CustomerProfile::with(['customer'])
            ->where('customer_id', $customerId)
            ->first();
    }

    public function findByCustomerCode(string $customerCode): ?CustomerProfile
    {
        return CustomerProfile::where('customer_code', $customerCode)->first();
    }

    public function getAll(): Collection
    {
        return CustomerProfile::with(['customer'])
            ->orderByDesc('id')
            ->get();
    }

    public function create(array $data): CustomerProfile
    {
        return CustomerProfile::create($data);
    }

    public function update(CustomerProfile $customerProfile, array $data): CustomerProfile
    {
        $customerProfile->update($data);

        return $customerProfile->fresh(['customer']);
    }

    public function delete(CustomerProfile $customerProfile): void
    {
        $customerProfile->delete();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return CustomerProfile::with(['customer'])
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function search(array $filters): Collection
    {
        $query = CustomerProfile::with(['customer']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('customer_code', 'like', "%{$filters['search']}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($filters) {
                        $customerQuery->where('name', 'like', "%{$filters['search']}%")
                            ->orWhere('email', 'like', "%{$filters['search']}%")
                            ->orWhere('phone', 'like', "%{$filters['search']}%");
                    });
            });
        }

        if (isset($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (isset($filters['marketing_opt_in'])) {
            $query->where('marketing_opt_in', $filters['marketing_opt_in']);
        }

        if (isset($filters['sort'])) {
            $direction = $filters['sort_direction'] ?? 'desc';
            $query->orderBy($filters['sort'], $direction);
        }

        return $query->get();
    }

    public function count(): int
    {
        return CustomerProfile::count();
    }

    public function countActive(): int
    {
        return CustomerProfile::where('status', CustomerProfile::STATUS_ACTIVE)->count();
    }

    public function countInactive(): int
    {
        return CustomerProfile::where('status', CustomerProfile::STATUS_INACTIVE)->count();
    }

    public function countBanned(): int
    {
        return CustomerProfile::where('status', CustomerProfile::STATUS_BANNED)->count();
    }
}
