<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\UserAddress;

class UserAddressPolicy
{
    public function viewAny(Customer $customer): bool
    {
        return true;
    }

    public function view(Customer $customer, UserAddress $address): bool
    {
        return $customer->id === $address->customer_id;
    }

    public function create(Customer $customer): bool
    {
        return true;
    }

    public function update(Customer $customer, UserAddress $address): bool
    {
        return $customer->id === $address->customer_id;
    }

    public function delete(Customer $customer, UserAddress $address): bool
    {
        return $customer->id === $address->customer_id;
    }
}
