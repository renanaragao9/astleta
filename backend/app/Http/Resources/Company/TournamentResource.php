<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\GenerateTemporaryUrlSHelper;

class TournamentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'description' => $this->description,
            'awards' => $this->awards,
            'welcomeEmail' => $this->welcome_email,
            'startDate' => $this->start_date?->format('d/m/Y'),
            'endDate' => $this->end_date?->format('d/m/Y'),
            'maxParticipants' => $this->max_participants,
            'isPublic' => $this->is_public,
            'created' => $this->created_at?->format('d/m/Y H:i:s'),
        ];
    }
}