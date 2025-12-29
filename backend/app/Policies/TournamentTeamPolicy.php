<?php

namespace App\Policies;

use App\Models\TournamentTeam;
use App\Models\User;
use App\Services\CheckPermissionsService;

class TournamentTeamPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-tournament-team');
    }

    public function view(User $user, TournamentTeam $tournamentTeam): bool
    {
        return CheckPermissionsService::run($user, 'show-tournament-team');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-tournament-team');
    }

    public function update(User $user, TournamentTeam $tournamentTeam): bool
    {
        return CheckPermissionsService::run($user, 'edit-tournament-team');
    }

    public function delete(User $user, TournamentTeam $tournamentTeam): bool
    {
        return CheckPermissionsService::run($user, 'delete-tournament-team');
    }

    public function restore(User $user, TournamentTeam $tournamentTeam): bool
    {
        return CheckPermissionsService::run($user, 'restore-tournament-team');
    }

    public function forceDelete(User $user, TournamentTeam $tournamentTeam): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-tournament-team');
    }
}