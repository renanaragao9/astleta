<?php

namespace App\Services\Api\Athlete\TeamStatisticsBooking;

use App\Models\TeamBooking;
use App\Models\TeamStatisticsBooking;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class IndexTeamStatisticsBookingService extends BaseService
{
    public function run(TeamBooking $teamBooking): Collection
    {
        $userId = $this->getUserId();

        $booking = $teamBooking->booking;

        if ($booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva de time não encontrada ou você não tem permissão para acessá-la.']);
        }

        return TeamStatisticsBooking::where('team_booking_id', $teamBooking->id)
            ->with([
                'team:id,name',
                'statisticTeam:id,name',
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
