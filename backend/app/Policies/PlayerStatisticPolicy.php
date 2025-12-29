<?php

namespace App\Policies;

use App\Models\PlayerStatistic;
use App\Models\User;
use App\Services\CheckPermissionsService;

class PlayerStatisticPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-player-statistic');
    }

    public function view(User $user, PlayerStatistic $playerStatistic): bool
    {
        return CheckPermissionsService::run($user, 'show-player-statistic');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-player-statistic');
    }

    public function update(User $user, PlayerStatistic $playerStatistic): bool
    {
        return CheckPermissionsService::run($user, 'edit-player-statistic');
    }

    public function delete(User $user, PlayerStatistic $playerStatistic): bool
    {
        return CheckPermissionsService::run($user, 'delete-player-statistic');
    }

    public function restore(User $user, PlayerStatistic $playerStatistic): bool
    {
        return CheckPermissionsService::run($user, 'restore-player-statistic');
    }

    public function forceDelete(User $user, PlayerStatistic $playerStatistic): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-player-statistic');
    }
}
