<?php

namespace App\Policies;

use App\Models\TeamBooking;
use App\Models\User;
use App\Services\CheckPermissionsService;

class TeamBookingPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-team-booking');
    }

    public function view(User $user, TeamBooking $teamBooking): bool
    {
        return CheckPermissionsService::run($user, 'show-team-booking');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-team-booking');
    }

    public function update(User $user, TeamBooking $teamBooking): bool
    {
        return CheckPermissionsService::run($user, 'edit-team-booking');
    }

    public function delete(User $user, TeamBooking $teamBooking): bool
    {
        return CheckPermissionsService::run($user, 'delete-team-booking');
    }

    public function restore(User $user, TeamBooking $teamBooking): bool
    {
        return CheckPermissionsService::run($user, 'restore-team-booking');
    }

    public function forceDelete(User $user, TeamBooking $teamBooking): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-team-booking');
    }
}
