<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;
use App\Services\CheckPermissionsService;

class ProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-profile');
    }

    public function view(User $user, Profile $profile): bool
    {
        return CheckPermissionsService::run($user, 'show-profile');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-profile');
    }

    public function update(User $user, Profile $profile): bool
    {
        return CheckPermissionsService::run($user, 'edit-profile');
    }

    public function delete(User $user, Profile $profile): bool
    {
        return CheckPermissionsService::run($user, 'delete-profile');
    }

    public function restore(User $user, Profile $profile): bool
    {
        return false;
    }

    public function forceDelete(User $user, Profile $profile): bool
    {
        return false;
    }
}
