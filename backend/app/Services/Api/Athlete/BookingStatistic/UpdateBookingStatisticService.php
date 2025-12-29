<?php

namespace App\Services\Api\Athlete\BookingStatistic;

use App\Models\Booking;
use App\Models\PlayerStatistic;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateBookingStatisticService extends BaseService
{
    public function run(array $data, Booking $booking, PlayerStatistic $playerStatistic): PlayerStatistic
    {
        $userId = $this->getUserId();

        if ($booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva não encontrada ou você não tem permissão para acessá-la.']);
        }

        if ($booking->booking_status !== 'concluido') {
            throw ValidationException::withMessages(['error' => 'Estatísticas só podem ser editadas em reservas concluídas.']);
        }

        if ($playerStatistic->booking_id !== $booking->id) {
            throw ValidationException::withMessages(['error' => 'Estatística não encontrada nesta reserva.']);
        }

        return DB::transaction(function () use ($data, $playerStatistic) {
            $playerStatistic->update($data);

            return $playerStatistic->fresh(['user', 'statistic']);
        });
    }
}
