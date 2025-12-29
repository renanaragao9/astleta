<?php

namespace App\Policies;

use App\Models\StockMovement;
use App\Models\User;
use App\Services\CheckPermissionsService;

class StockMovementPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-stock-movement');
    }

    public function view(User $user, StockMovement $stockMovement): bool
    {
        return CheckPermissionsService::run($user, 'show-stock-movement');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-stock-movement');
    }

    public function update(User $user, StockMovement $stockMovement): bool
    {
        return CheckPermissionsService::run($user, 'edit-stock-movement');
    }

    public function delete(User $user, StockMovement $stockMovement): bool
    {
        return CheckPermissionsService::run($user, 'delete-stock-movement');
    }

    public function restore(User $user, StockMovement $stockMovement): bool
    {
        return CheckPermissionsService::run($user, 'restore-stock-movement');
    }

    public function forceDelete(User $user, StockMovement $stockMovement): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-stock-movement');
    }
}