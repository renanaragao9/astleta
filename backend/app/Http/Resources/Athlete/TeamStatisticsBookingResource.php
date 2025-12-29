<?php

namespace App\Http\Resources\Athlete;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamStatisticsBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->resource->loadMissing(['team', 'statisticTeam']);

        return [
            'id' => $this->id,
            'count' => $this->count,
            'createdAt' => $this->created_at->format('d/m/Y H:i:s'),

            'team' => $this->whenLoaded('team', fn () => [
                'id' => $this->team->id,
                'name' => $this->team->name,
            ]),

            'statistic' => $this->whenLoaded('statisticTeam', fn () => [
                'id' => $this->statisticTeam->id,
                'name' => $this->statisticTeam->name,
            ]),
        ];
    }
}
