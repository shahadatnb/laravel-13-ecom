<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\Order;

class OrderPolicy
{
    public function viewAny(Customer $customer): bool
    {
        return $customer->can('view_any_order');
    }

    public function view(Customer $customer, Order $order): bool
    {
        if ($customer->id === $order->customer_id) {
            return true;
        }

        return $customer->can('view_order');
    }

    public function create(Customer $customer): bool
    {
        return true;
    }

    public function update(Customer $customer, Order $order): bool
    {
        return $customer->can('update_order');
    }

    public function delete(Customer $customer, Order $order): bool
    {
        return $customer->can('delete_order');
    }

    public function restore(Customer $customer, Order $order): bool
    {
        return $customer->can('restore_order');
    }

    public function forceDelete(Customer $customer, Order $order): bool
    {
        return $customer->can('force_delete_order');
    }

    public function approve(Customer $customer, Order $order): bool
    {
        return $customer->can('approve_order');
    }

    public function cancel(Customer $customer, Order $order): bool
    {
        return $customer->id === $order->customer_id || $customer->can('cancel_order');
    }
}
