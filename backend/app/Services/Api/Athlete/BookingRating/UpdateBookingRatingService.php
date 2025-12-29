<?php

namespace App\Services\Api\Athlete\BookingRating;

use App\Models\Booking;
use App\Models\PlayerRating;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateBookingRatingService extends BaseService
{
    public function run(array $data, Booking $booking, PlayerRating $playerRating): PlayerRating
    {
        $userId = $this->getUserId();

        if ($booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva não encontrada ou você não tem permissão para acessá-la.']);
        }

        if ($playerRating->booking_id !== $booking->id) {
            throw ValidationException::withMessages(['error' => 'Avaliação não encontrada nesta reserva.']);
        }

        return DB::transaction(function () use ($data, $playerRating) {
            $playerRating->update($data);

            return $playerRating->fresh(with: ['user', 'bookingParticipant']);
        });
    }
}
