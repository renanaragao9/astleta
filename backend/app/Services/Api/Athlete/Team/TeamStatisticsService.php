<?php

namespace App\Services\Api\Athlete\Team;

use App\Models\Team;
use App\Models\TeamBooking;
use App\Services\Api\Athlete\Global\BaseService;

class TeamStatisticsService extends BaseService
{
    public function run(Team $team): array
    {
        $teamBookings = TeamBooking::where('home_team_id', $team->id)
            ->orWhere('away_team_id', $team->id)
            ->get();

        $stats = [
            'wins' => 0,
            'losses' => 0,
            'draws' => 0,
            'goals_scored' => 0,
            'goals_conceded' => 0,
            'matches' => 0,
        ];

        foreach ($teamBookings as $booking) {
            if ($booking->home_team_id == $team->id) {
                $stats['matches']++;
                $stats['goals_scored'] += $booking->goals_home ?? 0;
                $stats['goals_conceded'] += $booking->goals_away ?? 0;

                if ($booking->winner_id == $team->id) {
                    $stats['wins']++;
                } elseif ($booking->winner_id === null) {
                    $stats['draws']++;
                } else {
                    $stats['losses']++;
                }
            } elseif ($booking->away_team_id == $team->id) {
                $stats['matches']++;
                $stats['goals_scored'] += $booking->goals_away ?? 0;
                $stats['goals_conceded'] += $booking->goals_home ?? 0;

                if ($booking->winner_id == $team->id) {
                    $stats['wins']++;
                } elseif ($booking->winner_id === null) {
                    $stats['draws']++;
                } else {
                    $stats['losses']++;
                }
            }
        }

        return $stats;
    }
}
