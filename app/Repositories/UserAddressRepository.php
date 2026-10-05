<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Collection;

class UserAddressRepository
{
    /**
     * All addresses for a customer, default address first.
     *
     * @return Collection<int, UserAddress>
     */
    public function allForCustomer(Customer $customer): Collection
    {
        return UserAddress::where('customer_id', $customer->id)
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): UserAddress
    {
        return UserAddress::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(UserAddress $address, array $data): UserAddress
    {
        $address->update($data);

        return $address;
    }

    public function delete(UserAddress $address): void
    {
        $address->delete();
    }

    /**
     * Clear the default flag on every other address of the customer.
     */
    public function clearDefault(int $customerId, ?int $exceptId = null): void
    {
        UserAddress::where('customer_id', $customerId)
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }
}
