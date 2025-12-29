<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use App\Services\CheckPermissionsService;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-booking');
    }

    public function view(User $user, Booking $booking): bool
    {
        return CheckPermissionsService::run($user, 'show-booking');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-booking');
    }

    public function update(User $user, Booking $booking): bool
    {
        return CheckPermissionsService::run($user, 'edit-booking');
    }

    public function delete(User $user, Booking $booking): bool
    {
        return CheckPermissionsService::run($user, 'delete-booking');
    }

    public function restore(User $user, Booking $booking): bool
    {
        return CheckPermissionsService::run($user, 'restore-booking');
    }

    public function forceDelete(User $user, Booking $booking): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-booking');
    }
}
