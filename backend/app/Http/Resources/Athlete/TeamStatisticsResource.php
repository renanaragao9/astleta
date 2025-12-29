<?php

namespace App\Http\Resources\Athlete;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamStatisticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'wins' => $this->resource['wins'],
            'losses' => $this->resource['losses'],
            'draws' => $this->resource['draws'],
            'goalScored' => $this->resource['goals_scored'],
            'goalConceded' => $this->resource['goals_conceded'],
            'matches' => $this->resource['matches'],
        ];
    }
}
