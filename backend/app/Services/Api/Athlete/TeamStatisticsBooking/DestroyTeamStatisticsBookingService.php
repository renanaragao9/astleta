<?php

namespace App\Services\Api\Athlete\TeamStatisticsBooking;

use App\Models\TeamBooking;
use App\Models\TeamStatisticsBooking;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Validation\ValidationException;

class DestroyTeamStatisticsBookingService extends BaseService
{
    public function run(TeamBooking $teamBooking, TeamStatisticsBooking $teamStatisticsBooking): void
    {
        $userId = $this->getUserId();

        $booking = $teamBooking->booking;

        if ($booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva de time não encontrada ou você não tem permissão para acessá-la.']);
        }

        if ($booking->booking_status !== 'concluido') {
            throw ValidationException::withMessages(['error' => 'Estatísticas de time só podem ser removidas de reservas concluídas.']);
        }

        if (! $teamBooking->is_friendly) {
            throw ValidationException::withMessages(['error' => 'Estatísticas de time só podem ser removidas de partidas amistaosas.']);
        }

        if ($teamStatisticsBooking->team_booking_id !== $teamBooking->id) {
            throw ValidationException::withMessages(['error' => 'Estatística de time não encontrada nesta reserva.']);
        }

        $teamStatisticsBooking->delete();
    }
}
