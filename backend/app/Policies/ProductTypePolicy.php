<?php

namespace App\Policies;

use App\Models\ProductType;
use App\Models\User;
use App\Services\CheckPermissionsService;

class ProductTypePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-product-type');
    }

    public function view(User $user, ProductType $productType): bool
    {
        return CheckPermissionsService::run($user, 'show-product-type');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-product-type');
    }

    public function update(User $user, ProductType $productType): bool
    {
        return CheckPermissionsService::run($user, 'edit-product-type');
    }

    public function delete(User $user, ProductType $productType): bool
    {
        return CheckPermissionsService::run($user, 'delete-product-type');
    }

    public function restore(User $user, ProductType $productType): bool
    {
        return CheckPermissionsService::run($user, 'edit-product-type');
    }

    public function forceDelete(User $user, ProductType $productType): bool
    {
        return CheckPermissionsService::run($user, 'delete-product-type');
    }
}
