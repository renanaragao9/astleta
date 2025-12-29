<?php

namespace App\Http\Resources\Athlete;

use App\Helpers\GenerateTemporaryUrlSHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingRatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $generateTemporaryUrl = new GenerateTemporaryUrlSHelper;

        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'technicalRating' => $this->technical_rating,
            'tacticalRating' => $this->tactical_rating,
            'physicalRating' => $this->physical_rating,
            'mentalRating' => $this->mental_rating,
            'teamworkRating' => $this->teamwork_rating,
            'comment' => $this->comment,
            'createdAt' => $this->created_at->format('d/m/Y H:i:s'),
            'updatedAt' => $this->updated_at->format('d/m/Y H:i:s'),

            'participant' => $this->whenLoaded('bookingParticipant', fn() => [
                'id' => $this->bookingParticipant->id,
                'name' => $this->bookingParticipant->name,
                'imagePath' => $this->bookingParticipant->user?->image_path ? $generateTemporaryUrl->run($this->bookingParticipant->user->image_path, 1) : null,
            ]),
        ];
    }
}
