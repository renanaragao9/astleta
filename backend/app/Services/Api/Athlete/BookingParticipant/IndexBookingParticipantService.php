<?php

namespace App\Services\Api\Athlete\BookingParticipant;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class IndexBookingParticipantService extends BaseService
{
    public function run(Booking $booking): Collection
    {
        $userId = $this->getUserId();

        if ($booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva não encontrada ou você não tem permissão para acessá-la.']);
        }

        return BookingParticipant::where('booking_id', $booking->id)
            ->with([
                'user:id,name,email,phone,image_path',
                'user.athleteProfile.feature:id,name',
                'user.athleteProfile.position:id,name',
                'addedByUser:id,name',
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
