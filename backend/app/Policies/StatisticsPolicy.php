<?php

namespace App\Policies;

use App\Models\Statistics;
use App\Models\User;
use App\Services\CheckPermissionsService;

class StatisticsPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-statistics');
    }

    public function view(User $user, Statistics $statistics): bool
    {
        return CheckPermissionsService::run($user, 'show-statistics');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-statistics');
    }

    public function update(User $user, Statistics $statistics): bool
    {
        return CheckPermissionsService::run($user, 'edit-statistics');
    }

    public function delete(User $user, Statistics $statistics): bool
    {
        return CheckPermissionsService::run($user, 'delete-statistics');
    }

    public function restore(User $user, Statistics $statistics): bool
    {
        return CheckPermissionsService::run($user, 'edit-statistics');
    }

    public function forceDelete(User $user, Statistics $statistics): bool
    {
        return CheckPermissionsService::run($user, 'delete-statistics');
    }
}
