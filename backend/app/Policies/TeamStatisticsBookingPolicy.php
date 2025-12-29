<?php

namespace App\Policies;

use App\Models\TeamStatisticsBooking;
use App\Models\User;
use App\Services\CheckPermissionsService;

class TeamStatisticsBookingPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-team-statistics-booking');
    }

    public function view(User $user, TeamStatisticsBooking $teamStatisticsBooking): bool
    {
        return CheckPermissionsService::run($user, 'show-team-statistics-booking');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-team-statistics-booking');
    }

    public function update(User $user, TeamStatisticsBooking $teamStatisticsBooking): bool
    {
        return CheckPermissionsService::run($user, 'edit-team-statistics-booking');
    }

    public function delete(User $user, TeamStatisticsBooking $teamStatisticsBooking): bool
    {
        return CheckPermissionsService::run($user, 'delete-team-statistics-booking');
    }

    public function restore(User $user, TeamStatisticsBooking $teamStatisticsBooking): bool
    {
        return CheckPermissionsService::run($user, 'restore-team-statistics-booking');
    }

    public function forceDelete(User $user, TeamStatisticsBooking $teamStatisticsBooking): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-team-statistics-booking');
    }
}
