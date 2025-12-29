<?php

namespace App\Http\Resources\Athlete;

use App\Helpers\GenerateTemporaryUrlSHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingStatisticResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->resource->loadMissing(['bookingParticipant', 'statistic']);
        $generateTemporaryUrl = new GenerateTemporaryUrlSHelper;

        return [
            'id' => $this->id,
            'count' => $this->count,
            'createdAt' => $this->created_at->format('d/m/Y H:i:s'),

            'participant' => $this->whenLoaded('bookingParticipant', fn() => [
                'id' => $this->bookingParticipant->id,
                'name' => $this->bookingParticipant->name,
                'imagePath' => $this->bookingParticipant->user?->image_path ? $generateTemporaryUrl->run($this->bookingParticipant->user->image_path, 1) : null,
            ]),

            'statistic' => $this->whenLoaded('statistic', fn() => [
                'id' => $this->statistic->id,
                'name' => $this->statistic->name,
            ]),
        ];
    }
}
