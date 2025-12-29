<?php

namespace App\Policies;

use App\Models\Purchase;
use App\Models\User;
use App\Services\CheckPermissionsService;

class PurchasePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-purchase');
    }

    public function view(User $user, Purchase $purchase): bool
    {
        return CheckPermissionsService::run($user, 'show-purchase');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-purchase');
    }

    public function update(User $user, Purchase $purchase): bool
    {
        return CheckPermissionsService::run($user, 'edit-purchase');
    }

    public function delete(User $user, Purchase $purchase): bool
    {
        return CheckPermissionsService::run($user, 'delete-purchase');
    }

    public function restore(User $user, Purchase $purchase): bool
    {
        return CheckPermissionsService::run($user, 'restore-purchase');
    }

    public function forceDelete(User $user, Purchase $purchase): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-purchase');
    }
}