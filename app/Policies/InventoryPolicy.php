<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view inventory');
    }

    public function view(User $user): bool
    {
        return $user->hasPermissionTo('view inventory');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create warehouse');
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo('edit warehouse');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('delete warehouse');
    }

    public function adjust(User $user): bool
    {
        return $user->hasPermissionTo('adjust inventory');
    }

    public function transfer(User $user): bool
    {
        return $user->hasPermissionTo('transfer inventory');
    }

    public function audit(User $user): bool
    {
        return $user->hasPermissionTo('audit inventory');
    }

    public function export(User $user): bool
    {
        return $user->hasPermissionTo('export inventory');
    }
}
