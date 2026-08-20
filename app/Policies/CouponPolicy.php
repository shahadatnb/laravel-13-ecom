<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouponPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view coupon');
    }

    public function view(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('view coupon');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create coupon');
    }

    public function update(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('edit coupon');
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('delete coupon');
    }

    public function export(User $user): bool
    {
        return $user->hasPermissionTo('export coupon');
    }
}
