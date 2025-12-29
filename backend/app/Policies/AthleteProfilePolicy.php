<?php

namespace App\Policies;

use App\Models\AthleteProfile;
use App\Models\User;
use App\Services\CheckPermissionsService;

class AthleteProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-athlete-profile');
    }

    public function view(User $user, AthleteProfile $athleteProfile): bool
    {
        return CheckPermissionsService::run($user, 'show-athlete-profile');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-athlete-profile');
    }

    public function update(User $user, AthleteProfile $athleteProfile): bool
    {
        return CheckPermissionsService::run($user, 'edit-athlete-profile');
    }

    public function delete(User $user, AthleteProfile $athleteProfile): bool
    {
        return CheckPermissionsService::run($user, 'delete-athlete-profile');
    }

    public function restore(User $user, AthleteProfile $athleteProfile): bool
    {
        return CheckPermissionsService::run($user, 'restore-athlete-profile');
    }

    public function forceDelete(User $user, AthleteProfile $athleteProfile): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-athlete-profile');
    }
}
