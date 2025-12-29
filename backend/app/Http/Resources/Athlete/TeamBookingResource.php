<?php

namespace App\Http\Resources\Athlete;

use App\Helpers\GenerateTemporaryUrlSHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $generateTemporaryUrl = new GenerateTemporaryUrlSHelper;

        return [
            'id' => $this->id,
            'bookingId' => $this->booking_id,
            'result' => $this->result,
            'goalsHome' => $this->goals_home,
            'goalsAway' => $this->goals_away,

            'homeTeam' => [
                'uuid' => $this->homeTeam?->uuid,
                'name' => $this->homeTeam?->name,
                'nickname' => $this->homeTeam?->nickname,
                'stadiumName' => $this->homeTeam?->stadium_name,
                'primaryColor' => $this->homeTeam?->primary_color,
                'secondaryColor' => $this->homeTeam?->secondary_color,
                'shieldPath' => $this->homeTeam?->shield_path ? $generateTemporaryUrl->run($this->homeTeam->shield_path, 1) : null,
            ],
            'awayTeam' => [
                'uuid' => $this->awayTeam?->uuid,
                'name' => $this->awayTeam?->name,
                'nickname' => $this->awayTeam?->nickname,
                'stadiumName' => $this->awayTeam?->stadium_name,
                'primaryColor' => $this->awayTeam?->primary_color,
                'secondaryColor' => $this->awayTeam?->secondary_color,
                'shieldPath' => $this->awayTeam?->shield_path ? $generateTemporaryUrl->run($this->awayTeam->shield_path, 1) : null,
            ],
            'sport' => [
                'id' => $this->sport?->id,
                'name' => $this->sport?->name,
            ],
            'winner' => [
                'id' => $this->winner?->id,
                'uuid' => $this->winner?->uuid,
                'name' => $this->winner?->name,
            ],
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
