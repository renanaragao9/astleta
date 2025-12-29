<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;
use App\Services\CheckPermissionsService;

class CouponPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-coupon');
    }

    public function view(User $user, Coupon $coupon): bool
    {
        return CheckPermissionsService::run($user, 'show-coupon');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-coupon');
    }

    public function update(User $user, Coupon $coupon): bool
    {
        return CheckPermissionsService::run($user, 'edit-coupon');
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return CheckPermissionsService::run($user, 'delete-coupon');
    }

    public function restore(User $user, Coupon $coupon): bool
    {
        return CheckPermissionsService::run($user, 'restore-coupon');
    }

    public function forceDelete(User $user, Coupon $coupon): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-coupon');
    }
}
