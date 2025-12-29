<?php

namespace App\Services\Api\Athlete\TeamBooking;

use App\Models\Booking;
use App\Models\Team;
use App\Models\TeamBooking;
use App\Services\BaseService;
use Illuminate\Validation\ValidationException;

class StoreTeamBookingService extends BaseService
{
    public function run(array $data, Booking $booking): TeamBooking
    {
        $this->validateBookingIsNotAlreadyAssigned($booking);

        $homeTeamId = $this->findTeamId($data, 'home_team');
        $awayTeamId = $this->findTeamId($data, 'away_team');

        $this->validateTeamsSport($homeTeamId, $awayTeamId, $data);

        return TeamBooking::create([
            'booking_id' => $booking->id,
            'home_team_id' => $homeTeamId,
            'away_team_id' => $awayTeamId,
            'is_friendly' => true,
            'sport_id' => $data['sport_id'] ?? null,
            'winner_id' => $data['winner_id'] ?? null,
        ])->load(['homeTeam', 'awayTeam', 'sport', 'winner']);
    }

    private function validateBookingIsNotAlreadyAssigned(Booking $booking): void
    {
        if (TeamBooking::where('booking_id', $booking->id)->exists()) {
            throw ValidationException::withMessages(['error' => 'Este jogo já possui times definidos']);
        }
    }

    private function findTeamId(array $data, string $type): ?int
    {
        $uuidKey = "{$type}_uuid";

        if (isset($data[$uuidKey])) {
            return Team::where('uuid', $data[$uuidKey])->first()?->id;
        }

        return null;
    }

    private function validateTeamsSport(?int $homeTeamId, ?int $awayTeamId, array $data): void
    {
        if (!$homeTeamId || !$awayTeamId) {
            return;
        }

        $homeTeam = Team::find($homeTeamId);
        $awayTeam = Team::find($awayTeamId);

        if ($homeTeam->sport_id !== $awayTeam->sport_id) {
            throw ValidationException::withMessages(['error' => 'Os times devem ter o mesmo esporte']);
        }

        if (isset($data['sport_id']) && $data['sport_id'] !== $homeTeam->sport_id) {
            throw ValidationException::withMessages(['error' => 'Apenas times do esporte definido são permitidos']);
        }
    }
}
