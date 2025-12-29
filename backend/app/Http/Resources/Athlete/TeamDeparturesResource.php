<?php

namespace App\Http\Resources\Athlete;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamDeparturesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'date' => $this->resource['date'],
            'opponent' => $this->resource['opponent'],
            'goalsScored' => $this->resource['goalsScored'],
            'goalsConceded' => $this->resource['goalsConceded'],
            'result' => $this->resource['result'],
            'isHome' => $this->resource['isHome'],
        ];
    }
}
