<?php

namespace App\Services\Api\Athlete\BookingRating;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\PlayerRating;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreBookingRatingService extends BaseService
{
    public function run(array $data, Booking $booking): PlayerRating
    {
        $userId = $this->getUserId();
        $bookingParicipantId = $data['booking_participant_id'];
        $participant = BookingParticipant::find($bookingParicipantId);

        if ($booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva não encontrada ou você não tem permissão para acessá-la.']);
        }

        if ($booking->booking_status !== 'concluido') {
            throw ValidationException::withMessages(['error' => 'Avaliações só podem ser adicionadas em reservas concluídas.']);
        }

        if (! $participant || $participant->booking_id !== $booking->id) {
            throw ValidationException::withMessages(['error' => 'Participante não encontrado nesta reserva.']);
        }

        $existingRating = PlayerRating::where([
            'booking_participant_id' => $data['booking_participant_id'],
            'booking_id' => $booking->id,
        ])->first();

        if ($existingRating) {
            throw ValidationException::withMessages(['error' => 'Já existe uma avaliação para este participante nesta reserva.']);
        }

        return DB::transaction(function () use ($data, $booking, $participant) {
            $data['booking_id'] = $booking->id;
            $data['user_id'] = $participant->user_id;

            return PlayerRating::create($data);
        });
    }
}
