<?php

namespace App\Http\Resources\Global;

use App\Helpers\GenerateTemporaryUrlSHelper;
use App\Services\Api\Athlete\Profile\CalculatePlayerRatingService;
use App\Services\Api\Athlete\Profile\CalculatePlayerStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicAthleteProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $generateTemporaryUrl = new GenerateTemporaryUrlSHelper;

        return [
            'user' => [
                'name' => $this->user->name,
                'image' => $this->user->image_path ? $generateTemporaryUrl->run($this->user->image_path, 1) : null,
            ],
            'team' => $this->user->team ? [
                'name' => $this->user->team->name,
                'shieldPath' => $this->user->team->shield_path ? $generateTemporaryUrl->run($this->user->team->shield_path, 1) : null,
            ] : null,
            'sport' => $this->whenLoaded('sport', [
                'name' => $this->sport->name,
            ]),
            'position' => $this->whenLoaded('position', [
                'name' => $this->position->name,
            ]),
            'subposition' => $this->whenLoaded('subposition', [
                'name' => $this->subposition->name,
            ]),
            'feature' => $this->whenLoaded('feature', [
                'name' => $this->feature->name,
            ]),
            'subfeature' => $this->whenLoaded('subfeature', [
                'name' => $this->subfeature->name,
            ]),
            'dominantSide' => $this->dominant_side,
            'height' => $this->height,
            'weight' => $this->weight,
            'bio' => $this->bio,
            'statistics' => $this->getPlayerStatistics(),
            'rating' => $this->getPlayerRating(),
        ];
    }

    private function getPlayerStatistics(): array
    {
        $calculateStatisticsService = new CalculatePlayerStatisticsService;

        return $calculateStatisticsService->run($this->user);
    }

    private function getPlayerRating(): float
    {
        $calculateRatingService = new CalculatePlayerRatingService;
        $ratings = $calculateRatingService->run($this->user);

        return $ratings['average'] ?? 0.0;
    }
}
