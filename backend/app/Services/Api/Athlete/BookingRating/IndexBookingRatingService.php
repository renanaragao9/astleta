<?php

namespace App\Services\Api\Athlete\BookingRating;

use App\Models\Booking;
use App\Models\PlayerRating;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class IndexBookingRatingService extends BaseService
{
    public function run(Booking $booking): Collection
    {
        $userId = $this->getUserId();

        if ($booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva não encontrada ou você não tem permissão para acessá-la.']);
        }

        return PlayerRating::where('booking_id', $booking->id)
            ->with([
                'user:id,name,email,image_path',
                'bookingParticipant.user:id,name,email,image_path',
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
