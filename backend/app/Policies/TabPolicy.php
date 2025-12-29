<?php

namespace App\Policies;

use App\Models\Tab;
use App\Models\User;
use App\Services\CheckPermissionsService;

class TabPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-tab');
    }

    public function view(User $user, Tab $tab): bool
    {
        return CheckPermissionsService::run($user, 'show-tab');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-tab');
    }

    public function update(User $user, Tab $tab): bool
    {
        return CheckPermissionsService::run($user, 'edit-tab');
    }

    public function delete(User $user, Tab $tab): bool
    {
        return CheckPermissionsService::run($user, 'delete-tab');
    }

    public function restore(User $user, Tab $tab): bool
    {
        return CheckPermissionsService::run($user, 'restore-tab');
    }

    public function forceDelete(User $user, Tab $tab): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-tab');
    }
}
