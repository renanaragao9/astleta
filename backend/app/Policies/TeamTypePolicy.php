<?php

namespace App\Policies;

use App\Models\TeamType;
use App\Models\User;
use App\Services\CheckPermissionsService;

class TeamTypePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-team-type');
    }

    public function view(User $user, TeamType $teamType): bool
    {
        return CheckPermissionsService::run($user, 'show-team-type');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-team-type');
    }

    public function update(User $user, TeamType $teamType): bool
    {
        return CheckPermissionsService::run($user, 'edit-team-type');
    }

    public function delete(User $user, TeamType $teamType): bool
    {
        return CheckPermissionsService::run($user, 'delete-team-type');
    }

    public function restore(User $user, TeamType $teamType): bool
    {
        return CheckPermissionsService::run($user, 'edit-team-type');
    }

    public function forceDelete(User $user, TeamType $teamType): bool
    {
        return CheckPermissionsService::run($user, 'delete-team-type');
    }
}
