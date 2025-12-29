<?php

namespace App\Policies;

use App\Models\TeamPlayer;
use App\Models\User;
use App\Services\CheckPermissionsService;

class TeamPlayerPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-team-player');
    }

    public function view(User $user, TeamPlayer $teamPlayer): bool
    {
        return CheckPermissionsService::run($user, 'show-team-player');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-team-player');
    }

    public function update(User $user, TeamPlayer $teamPlayer): bool
    {
        return CheckPermissionsService::run($user, 'edit-team-player');
    }

    public function delete(User $user, TeamPlayer $teamPlayer): bool
    {
        return CheckPermissionsService::run($user, 'delete-team-player');
    }

    public function restore(User $user, TeamPlayer $teamPlayer): bool
    {
        return CheckPermissionsService::run($user, 'restore-team-player');
    }

    public function forceDelete(User $user, TeamPlayer $teamPlayer): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-team-player');
    }
}
