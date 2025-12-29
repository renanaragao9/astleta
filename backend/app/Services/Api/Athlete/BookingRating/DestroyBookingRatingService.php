<?php

namespace App\Services\Api\Athlete\BookingRating;

use App\Models\Booking;
use App\Models\PlayerRating;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Validation\ValidationException;

class DestroyBookingRatingService extends BaseService
{
    public function run(Booking $booking, PlayerRating $playerRating): void
    {
        $userId = $this->getUserId();

        if ($booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva não encontrada ou você não tem permissão para acessá-la.']);
        }

        if ($booking->booking_status !== 'concluido') {
            throw ValidationException::withMessages(['error' => 'Avaliações só podem ser removidas de reservas concluídas.']);
        }

        if ($playerRating->booking_id !== $booking->id) {
            throw ValidationException::withMessages(['error' => 'Avaliação não encontrada nesta reserva.']);
        }

        $playerRating->delete();
    }
}
