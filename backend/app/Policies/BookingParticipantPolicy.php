<?php

namespace App\Policies;

use App\Models\BookingParticipant;
use App\Models\User;
use App\Services\CheckPermissionsService;

class BookingParticipantPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-booking-participant');
    }

    public function view(User $user, BookingParticipant $bookingParticipant): bool
    {
        return CheckPermissionsService::run($user, 'show-booking-participant');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-booking-participant');
    }

    public function update(User $user, BookingParticipant $bookingParticipant): bool
    {
        return CheckPermissionsService::run($user, 'edit-booking-participant');
    }

    public function delete(User $user, BookingParticipant $bookingParticipant): bool
    {
        return CheckPermissionsService::run($user, 'delete-booking-participant');
    }
}
