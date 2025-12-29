<?php

namespace App\Policies;

use App\Models\StatisticsTeam;
use App\Models\User;
use App\Services\CheckPermissionsService;

class StatisticsTeamPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-statistics-team');
    }

    public function view(User $user, StatisticsTeam $statisticsTeam): bool
    {
        return CheckPermissionsService::run($user, 'show-statistics-team');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-statistics-team');
    }

    public function update(User $user, StatisticsTeam $statisticsTeam): bool
    {
        return CheckPermissionsService::run($user, 'edit-statistics-team');
    }

    public function delete(User $user, StatisticsTeam $statisticsTeam): bool
    {
        return CheckPermissionsService::run($user, 'delete-statistics-team');
    }

    public function restore(User $user, StatisticsTeam $statisticsTeam): bool
    {
        return CheckPermissionsService::run($user, 'restore-statistics-team');
    }

    public function forceDelete(User $user, StatisticsTeam $statisticsTeam): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-statistics-team');
    }
}
