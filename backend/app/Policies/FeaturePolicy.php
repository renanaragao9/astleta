<?php

namespace App\Policies;

use App\Models\Feature;
use App\Models\User;
use App\Services\CheckPermissionsService;

class FeaturePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-feature');
    }

    public function view(User $user, Feature $feature): bool
    {
        return CheckPermissionsService::run($user, 'show-feature');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-feature');
    }

    public function update(User $user, Feature $feature): bool
    {
        return CheckPermissionsService::run($user, 'edit-feature');
    }

    public function delete(User $user, Feature $feature): bool
    {
        return CheckPermissionsService::run($user, 'delete-feature');
    }

    public function restore(User $user, Feature $feature): bool
    {
        return CheckPermissionsService::run($user, 'edit-feature');
    }

    public function forceDelete(User $user, Feature $feature): bool
    {
        return CheckPermissionsService::run($user, 'edit-feature');
    }
}
