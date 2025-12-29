<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Services\CheckPermissionsService;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-product');
    }

    public function view(User $user, Product $product): bool
    {
        return CheckPermissionsService::run($user, 'show-product');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-product');
    }

    public function update(User $user, Product $product): bool
    {
        return CheckPermissionsService::run($user, 'edit-product');
    }

    public function delete(User $user, Product $product): bool
    {
        return CheckPermissionsService::run($user, 'delete-product');
    }

    public function restore(User $user, Product $product): bool
    {
        return CheckPermissionsService::run($user, 'restore-product');
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-product');
    }
}
