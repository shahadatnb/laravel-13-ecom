<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\UserAddress;
use App\Repositories\UserAddressRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AddressService
{
    public function __construct(private readonly UserAddressRepository $repository) {}

    /**
     * List every address belonging to the customer.
     *
     * @return Collection<int, UserAddress>
     */
    public function listAddresses(Customer $customer): Collection
    {
        return $this->repository->allForCustomer($customer);
    }

    /**
     * Create an address for the customer.
     *
     * @param  array<string, mixed>  $data
     */
    public function createAddress(Customer $customer, array $data): UserAddress
    {
        return DB::transaction(function () use ($customer, $data) {
            $data['customer_id'] = $customer->id;

            if (! empty($data['is_default'])) {
                $this->repository->clearDefault($customer->id);
            }

            return $this->repository->create($data);
        });
    }

    /**
     * Update one of the customer's addresses, keeping a single default.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateAddress(Customer $customer, UserAddress $address, array $data): UserAddress
    {
        return DB::transaction(function () use ($customer, $address, $data) {
            if (! empty($data['is_default'])) {
                $this->repository->clearDefault($customer->id, $address->id);
            }

            return $this->repository->update($address, $data);
        });
    }

    /**
     * Soft delete an address.
     */
    public function deleteAddress(UserAddress $address): void
    {
        $this->repository->delete($address);
    }
}
