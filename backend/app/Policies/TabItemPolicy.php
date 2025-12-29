<?php

namespace App\Policies;

use App\Models\TabItem;
use App\Models\User;
use App\Services\CheckPermissionsService;

class TabItemPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-tab-item');
    }

    public function view(User $user, TabItem $tabItem): bool
    {
        return CheckPermissionsService::run($user, 'show-tab-item');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-tab-item');
    }

    public function update(User $user, TabItem $tabItem): bool
    {
        return CheckPermissionsService::run($user, 'edit-tab-item');
    }

    public function delete(User $user, TabItem $tabItem): bool
    {
        return CheckPermissionsService::run($user, 'delete-tab-item');
    }

    public function restore(User $user, TabItem $tabItem): bool
    {
        return CheckPermissionsService::run($user, 'restore-tab-item');
    }

    public function forceDelete(User $user, TabItem $tabItem): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-tab-item');
    }
}
