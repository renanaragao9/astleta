<?php

namespace App\Services\Api\Athlete\Team;

use App\Models\Team;
use App\Models\TeamBooking;
use App\Services\Api\Athlete\Global\BaseService;

class TeamDeparturesService extends BaseService
{
    public function run(Team $team): array
    {
        $teamBookings = TeamBooking::query()
            ->where('home_team_id', $team->id)
            ->orWhere('away_team_id', $team->id)
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('created_at', 'desc')
            ->get();

        $departures = [];

        foreach ($teamBookings as $booking) {
            $isHome = $booking->home_team_id == $team->id;

            $departure = [
                'id' => $booking->id,
                'date' => $booking->created_at?->format('d/m/Y') ?? null,
                'opponent' => $isHome ? $booking->awayTeam?->name : $booking->homeTeam?->name,
                'goalsScored' => $isHome ? $booking->goals_home : $booking->goals_away,
                'goalsConceded' => $isHome ? $booking->goals_away : $booking->goals_home,
                'result' => $this->calculateResult($booking, $isHome),
                'isHome' => $isHome,
            ];

            $departures[] = $departure;
        }

        return $departures;
    }

    private function calculateResult(TeamBooking $booking, bool $isHome): string
    {
        $goalsScored = $isHome ? $booking->goals_home : $booking->goals_away;
        $goalsConceded = $isHome ? $booking->goals_away : $booking->goals_home;

        if ($goalsScored > $goalsConceded) {
            return 'vitória';
        } elseif ($goalsScored < $goalsConceded) {
            return 'derrota';
        } elseif ($goalsScored === $goalsConceded) {
            return 'empate';
        }

        return 'pendente';
    }
}
