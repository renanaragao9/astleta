<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use App\Services\CheckPermissionsService;

class PermissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-permission');

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Permission $permission): bool
    {
        return CheckPermissionsService::run($user, 'show-permission');

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-permission');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission): bool
    {
        return CheckPermissionsService::run($user, 'edit-permission');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission): bool
    {
        return CheckPermissionsService::run($user, 'delete-permission');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Permission $permission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Permission $permission): bool
    {
        return false;
    }
}
