<?php

namespace App\Services\Api\Athlete\Profile;

use App\Models\PlayerRating;
use App\Models\User;
use App\Services\Api\Athlete\Global\BaseService;

class CalculatePlayerRatingService extends BaseService
{
    public function run(User $user): array
    {
        $ratings = PlayerRating::where('user_id', $user->id)->get();

        if ($ratings->isEmpty()) {
            return [
                'overallAverage' => 0,
                'technicalAverage' => 0,
                'tacticalAverage' => 0,
                'physicalAverage' => 0,
                'mentalAverage' => 0,
                'teamworkAverage' => 0,
                'totalRatings' => 0,
                'recentRatings' => [],
            ];
        }

        $overallAverage = round($ratings->avg('rating'), 1);
        $technicalAverage = round($ratings->avg('technical_rating'), 1);
        $tacticalAverage = round($ratings->avg('tactical_rating'), 1);
        $physicalAverage = round($ratings->avg('physical_rating'), 1);
        $mentalAverage = round($ratings->avg('mental_rating'), 1);
        $teamworkAverage = round($ratings->avg('teamwork_rating'), 1);

        $recentRatings = PlayerRating::where('user_id', $user->id)
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->with(['booking:id,booking_number,booking_date', 'bookingParticipant'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($rating) {
                return [
                    'rating' => $rating->rating,
                    'comment' => $rating->comment,
                    'booking_name' => $rating->booking->booking_number ?? 'Racha',
                    'date' => $rating->created_at->format('d/m/Y'),
                ];
            });

        return [
            'overallAverage' => $overallAverage,
            'technicalAverage' => $technicalAverage,
            'tacticalAverage' => $tacticalAverage,
            'physicalAverage' => $physicalAverage,
            'mentalAverage' => $mentalAverage,
            'teamworkAverage' => $teamworkAverage,
            'totalRatings' => $ratings->count(),
            'recentRatings' => $recentRatings,
        ];
    }
}
