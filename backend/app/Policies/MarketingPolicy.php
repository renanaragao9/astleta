<?php

namespace App\Policies;

use App\Models\Marketing;
use App\Models\User;
use App\Services\CheckPermissionsService;

class MarketingPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-marketing');
    }

    public function view(User $user, Marketing $marketing): bool
    {
        return CheckPermissionsService::run($user, 'show-marketing');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-marketing');
    }

    public function update(User $user, Marketing $marketing): bool
    {
        return CheckPermissionsService::run($user, 'edit-marketing');
    }

    public function delete(User $user, Marketing $marketing): bool
    {
        return CheckPermissionsService::run($user, 'delete-marketing');
    }

    public function restore(User $user, Marketing $marketing): bool
    {
        return CheckPermissionsService::run($user, 'restore-marketing');
    }

    public function forceDelete(User $user, Marketing $marketing): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-marketing');
    }
}
