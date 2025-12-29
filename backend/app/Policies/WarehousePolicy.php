<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warehouse;
use App\Services\CheckPermissionsService;

class WarehousePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-warehouse');
    }

    public function view(User $user, Warehouse $warehouse): bool
    {
        return CheckPermissionsService::run($user, 'show-warehouse');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-warehouse');
    }

    public function update(User $user, Warehouse $warehouse): bool
    {
        return CheckPermissionsService::run($user, 'edit-warehouse');
    }

    public function delete(User $user, Warehouse $warehouse): bool
    {
        return CheckPermissionsService::run($user, 'delete-warehouse');
    }

    public function restore(User $user, Warehouse $warehouse): bool
    {
        return CheckPermissionsService::run($user, 'restore-warehouse');
    }

    public function forceDelete(User $user, Warehouse $warehouse): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-warehouse');
    }
}