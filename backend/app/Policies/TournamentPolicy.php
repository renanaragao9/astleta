<?php

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;
use App\Services\CheckPermissionsService;

class TournamentPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-tournament');
    }

    public function view(User $user, Tournament $tournament): bool
    {
        return CheckPermissionsService::run($user, 'show-tournament');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-tournament');
    }

    public function update(User $user, Tournament $tournament): bool
    {
        return CheckPermissionsService::run($user, 'edit-tournament');
    }

    public function delete(User $user, Tournament $tournament): bool
    {
        return CheckPermissionsService::run($user, 'delete-tournament');
    }

    public function restore(User $user, Tournament $tournament): bool
    {
        return CheckPermissionsService::run($user, 'restore-tournament');
    }

    public function forceDelete(User $user, Tournament $tournament): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-tournament');
    }
}