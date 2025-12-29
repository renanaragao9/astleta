<?php

namespace App\Services\Api\Athlete\TeamBooking;

use App\Models\Booking;
use App\Models\TeamBooking;
use App\Services\BaseService;

class DestroyTeamBookingService extends BaseService
{
    public function run(Booking $booking): void
    {
        $teamBooking = TeamBooking::where('booking_id', $booking->id)->firstOrFail();
        $teamBooking->delete();
    }
}
