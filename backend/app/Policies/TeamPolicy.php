<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use App\Services\CheckPermissionsService;

class TeamPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-team');
    }

    public function view(User $user, Team $team): bool
    {
        return CheckPermissionsService::run($user, 'show-team');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-team');
    }

    public function update(User $user, Team $team): bool
    {
        return CheckPermissionsService::run($user, 'edit-team');
    }

    public function delete(User $user, Team $team): bool
    {
        return CheckPermissionsService::run($user, 'delete-team');
    }

    public function restore(User $user, Team $team): bool
    {
        return CheckPermissionsService::run($user, 'restore-team');
    }

    public function forceDelete(User $user, Team $team): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-team');
    }
}
