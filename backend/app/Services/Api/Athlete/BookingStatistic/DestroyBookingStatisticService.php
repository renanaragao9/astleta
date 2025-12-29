<?php

namespace App\Services\Api\Athlete\BookingStatistic;

use App\Models\Booking;
use App\Models\PlayerStatistic;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Validation\ValidationException;

class DestroyBookingStatisticService extends BaseService
{
    public function run(Booking $booking, PlayerStatistic $playerStatistic): void
    {
        $userId = $this->getUserId();

        if ($booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva não encontrada ou você não tem permissão para acessá-la.']);
        }

        if ($booking->booking_status !== 'concluido') {
            throw ValidationException::withMessages(['error' => 'Estatísticas só podem ser removidas de reservas concluídas.']);
        }

        if ($playerStatistic->booking_id !== $booking->id) {
            throw ValidationException::withMessages(['error' => 'Estatística não encontrada nesta reserva.']);
        }

        $playerStatistic->delete();
    }
}
