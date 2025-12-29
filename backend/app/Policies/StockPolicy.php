<?php

namespace App\Policies;

use App\Models\Stock;
use App\Models\User;
use App\Services\CheckPermissionsService;

class StockPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-stock');
    }

    public function view(User $user, Stock $stock): bool
    {
        return CheckPermissionsService::run($user, 'show-stock');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-stock');
    }

    public function update(User $user, Stock $stock): bool
    {
        return CheckPermissionsService::run($user, 'edit-stock');
    }

    public function delete(User $user, Stock $stock): bool
    {
        return CheckPermissionsService::run($user, 'delete-stock');
    }

    public function restore(User $user, Stock $stock): bool
    {
        return CheckPermissionsService::run($user, 'restore-stock');
    }

    public function forceDelete(User $user, Stock $stock): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-stock');
    }
}