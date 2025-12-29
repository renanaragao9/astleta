<?php

namespace App\Policies;

use App\Models\User;
use App\Services\CheckPermissionsService;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-user');
    }

    public function view(User $user, User $userToShow): bool
    {
        return CheckPermissionsService::run($user, 'show-user');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-user');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $userToHandler): bool
    {
        return CheckPermissionsService::run($user, 'edit-user');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function inactive(User $user, User $userToHandler): bool
    {
        return CheckPermissionsService::run($user, 'inactive-user');
    }
}
