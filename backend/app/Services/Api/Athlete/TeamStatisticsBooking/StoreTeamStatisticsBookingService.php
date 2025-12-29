<?php

namespace App\Services\Api\Athlete\TeamStatisticsBooking;

use App\Models\TeamBooking;
use App\Models\TeamStatisticsBooking;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreTeamStatisticsBookingService extends BaseService
{
    public function run(array $data, TeamBooking $teamBooking): TeamStatisticsBooking
    {
        $userId = $this->getUserId();

        $booking = $teamBooking->booking;

        if ($booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva de time não encontrada ou você não tem permissão para acessá-la.']);
        }

        if ($booking->booking_status !== 'concluido') {
            throw ValidationException::withMessages(['error' => 'Estatísticas de time só podem ser adicionadas em reservas concluídas.']);
        }

        if (! $teamBooking->is_friendly) {
            throw ValidationException::withMessages(['error' => 'Estatísticas de time só podem ser adicionadas em partidas amistaosas.']);
        }

        $existingStatistic = TeamStatisticsBooking::where([
            'team_id' => $data['team_id'],
            'statistic_id' => $data['statistic_id'],
            'team_booking_id' => $teamBooking->id,
        ])->first();

        if ($existingStatistic) {
            throw ValidationException::withMessages(['error' => 'Estatística já registrada para este time nesta reserva.']);
        }

        return DB::transaction(function () use ($data, $teamBooking) {
            $data['team_booking_id'] = $teamBooking->id;
            $data['booking_id'] = $teamBooking->booking_id;

            return TeamStatisticsBooking::create($data);
        });
    }
}
