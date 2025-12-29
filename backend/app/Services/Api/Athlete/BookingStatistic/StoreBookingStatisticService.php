<?php

namespace App\Services\Api\Athlete\BookingStatistic;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\PlayerStatistic;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreBookingStatisticService extends BaseService
{
    public function run(array $data, Booking $booking): PlayerStatistic
    {
        $userId = $this->getUserId();

        $existingStatistic = PlayerStatistic::where([
            'booking_participant_id' => $data['booking_participant_id'],
            'statistic_id' => $data['statistic_id'],
            'booking_id' => $booking->id,
        ])->first();

        if ($booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva não encontrada ou você não tem permissão para acessá-la.']);
        }

        if ($booking->booking_status !== 'concluido') {
            throw ValidationException::withMessages(['error' => 'Estatísticas só podem ser adicionadas em reservas concluídas.']);
        }

        if ($existingStatistic) {
            throw ValidationException::withMessages(['error' => 'Estatística já registrada para este jogador nesta reserva.']);
        }

        return DB::transaction(function () use ($data, $booking) {
            $data['booking_id'] = $booking->id;

            $participant = BookingParticipant::find($data['booking_participant_id']);
            if ($participant && $participant->user_id) {
                $data['user_id'] = $participant->user_id;
            }

            return PlayerStatistic::create($data);
        });
    }
}
