<?php

namespace App\Policies;

use App\Models\Sport;
use App\Models\User;
use App\Services\CheckPermissionsService;

class SportPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-sport');
    }

    public function view(User $user, Sport $sport): bool
    {
        return CheckPermissionsService::run($user, 'show-sport');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-sport');
    }

    public function update(User $user, Sport $sport): bool
    {
        return CheckPermissionsService::run($user, 'edit-sport');
    }

    public function delete(User $user, Sport $sport): bool
    {
        return CheckPermissionsService::run($user, 'delete-sport');
    }

    public function restore(User $user, Sport $sport): bool
    {
        return CheckPermissionsService::run($user, 'edit-sport');
    }

    public function forceDelete(User $user, Sport $sport): bool
    {
        return CheckPermissionsService::run($user, 'delete-sport');
    }
}
