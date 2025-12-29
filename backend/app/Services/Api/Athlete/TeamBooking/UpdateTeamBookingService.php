<?php

namespace App\Services\Api\Athlete\TeamBooking;

use App\Models\Booking;
use App\Models\TeamBooking;
use App\Services\BaseService;

class UpdateTeamBookingService extends BaseService
{
    public function run(array $data, Booking $booking): TeamBooking
    {
        $teamBooking = TeamBooking::where('booking_id', $booking->id)->firstOrFail();

        $updateData = $this->prepareUpdateData($data, $teamBooking);

        if (! empty($updateData)) {
            $teamBooking->update($updateData);
        }

        return $teamBooking->load(['homeTeam', 'awayTeam', 'sport', 'winner']);
    }

    private function prepareUpdateData(array $data, TeamBooking $teamBooking): array
    {
        $updateData = [];

        if (isset($data['goals_home'])) {
            $updateData['goals_home'] = $data['goals_home'];
        }

        if (isset($data['goals_away'])) {
            $updateData['goals_away'] = $data['goals_away'];
        }

        if (isset($data['sport_id'])) {
            $updateData['sport_id'] = $data['sport_id'];
        }

        if (isset($data['goals_home']) && isset($data['goals_away'])) {
            $goalsHome = $data['goals_home'];
            $goalsAway = $data['goals_away'];

            if ($goalsHome > $goalsAway) {
                $updateData['result'] = 'vitoria';
                $updateData['winner_id'] = $teamBooking->home_team_id;
            } elseif ($goalsAway > $goalsHome) {
                $updateData['result'] = 'derrota';
                $updateData['winner_id'] = $teamBooking->away_team_id;
            } else {
                $updateData['result'] = 'empate';
                $updateData['winner_id'] = null;
            }
        }

        return $updateData;
    }
}
