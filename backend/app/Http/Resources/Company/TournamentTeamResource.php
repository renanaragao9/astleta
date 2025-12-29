<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\GenerateTemporaryUrlSHelper;

class TournamentTeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tournamentId' => $this->tournament_id,
            'teamId' => $this->team_id,
            'points' => $this->points,
            'position' => $this->position,
            'wins' => $this->wins,
            'draws' => $this->draws,
            'losses' => $this->losses,
            'created' => $this->created_at?->format('d/m/Y H:i:s'),

            'team' => $this->whenLoaded('team', function () {
                $generateTemporaryUrl = new GenerateTemporaryUrlSHelper;
                return [
                    'id' => $this->team->id,
                    'name' => $this->team->name,
                    'shieldPath' => $this->team->shield_path ? $generateTemporaryUrl->run($this->team->shield_path, 1) : null,
                ];
            }),
        ];
    }
}