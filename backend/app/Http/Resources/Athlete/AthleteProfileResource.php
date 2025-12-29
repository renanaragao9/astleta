<?php

namespace App\Http\Resources\Athlete;

use App\Helpers\GenerateTemporaryUrlSHelper;
use App\Services\Api\Athlete\Profile\CalculatePlayerRatingService;
use App\Services\Api\Athlete\Profile\CalculatePlayerStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AthleteProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $generateTemporaryUrl = new GenerateTemporaryUrlSHelper;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'date' => $this->date?->format('d/m/Y'),
            'lang' => $this->lang,
            'isPublic' => $this->is_public,
            'imagePath' => $this->image_path ? $generateTemporaryUrl->run($this->image_path, 1) : null,
            'createdAt' => $this->created_at?->format('d/m/Y H:i:s'),

            'athleteProfile' => $this->whenLoaded('athleteProfile', fn () => [
                'id' => $this->athleteProfile->id,
                'dominantSide' => $this->athleteProfile->dominant_side,
                'height' => $this->athleteProfile->height,
                'weight' => $this->athleteProfile->weight,
                'bio' => $this->athleteProfile->bio,
                'createdAt' => $this->athleteProfile->created_at,
                'sport' => $this->athleteProfile->sport,
                'position' => $this->athleteProfile->position,
                'subposition' => $this->athleteProfile->subposition,
                'feature' => $this->athleteProfile->feature,
                'subfeature' => $this->athleteProfile->subfeature,
            ]),

            'team' => $this->team ? [
                'name' => $this->team->name,
                'shieldPath' => $this->team->shield_path ? $generateTemporaryUrl->run($this->team->shield_path, 1) : null,
            ] : null,

            'skills' => $this->whenLoaded('skills', fn () => $this->skills->map(fn ($skill) => ['id' => $skill->id, 'name' => $skill->name])),

            'statistics' => $this->getPlayerStatistics(),
            'ratings' => $this->getPlayerRatings(),
        ];
    }

    private function getPlayerStatistics(): array
    {
        $calculateStatisticsService = new CalculatePlayerStatisticsService;

        return $calculateStatisticsService->run($this->resource);
    }

    private function getPlayerRatings(): array
    {
        $calculateRatingService = new CalculatePlayerRatingService;

        return $calculateRatingService->run($this->resource);
    }
}
