<?php

namespace App\Services\Api\Athlete\TeamBooking;

use App\Models\Booking;
use App\Models\TeamBooking;
use App\Services\BaseService;

class IndexTeamBookingService extends BaseService
{
    public function run(Booking $booking): ?TeamBooking
    {
        return TeamBooking::with(['homeTeam', 'awayTeam', 'sport', 'winner'])
            ->where('booking_id', $booking->id)
            ->first();
    }
}
