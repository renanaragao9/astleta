<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;
use App\Services\CheckPermissionsService;

class PositionPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-position');
    }

    public function view(User $user, Position $position): bool
    {
        return CheckPermissionsService::run($user, 'show-position');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-position');
    }

    public function update(User $user, Position $position): bool
    {
        return CheckPermissionsService::run($user, 'edit-position');
    }

    public function delete(User $user, Position $position): bool
    {
        return CheckPermissionsService::run($user, 'delete-position');
    }

    public function restore(User $user, Position $position): bool
    {
        return CheckPermissionsService::run($user, 'edit-position');
    }

    public function forceDelete(User $user, Position $position): bool
    {
        return CheckPermissionsService::run($user, 'delete-position');
    }
}
