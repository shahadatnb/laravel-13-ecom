<?php

namespace App\Policies;

use App\Models\CustomerProfile;
use App\Models\User;

class CustomerProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // return $user->can('view-any customer-profile');
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CustomerProfile $customerProfile): bool
    {
        // return $user->can('view customer-profile');
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // return $user->can('create customer-profile');
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CustomerProfile $customerProfile): bool
    {
        // return $user->can('update customer-profile');
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CustomerProfile $customerProfile): bool
    {
        // return $user->can('delete customer-profile');
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CustomerProfile $customerProfile): bool
    {
        // return $user->can('restore customer-profile');
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CustomerProfile $customerProfile): bool
    {
        // return $user->can('force-delete customer-profile');
        return false;
    }

    /**
     * Determine whether the user can update the customer status.
     */
    public function updateStatus(User $user, CustomerProfile $customerProfile): bool
    {
        // return $user->can('update-status customer-profile');
        return false;
    }
}
