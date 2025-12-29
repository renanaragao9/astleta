<?php

namespace App\Services\Api\Athlete\Racha;

use App\Models\BookingParticipant;
use App\Models\PlayerRating;
use App\Models\PlayerStatistic;
use App\Services\Api\Athlete\Global\BaseService;
use App\Traits\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexAthleteRachaService extends BaseService
{
    use Sortable;

    public function run(array $data): LengthAwarePaginator
    {
        $userId = $this->getUserId();
        $search = $data['search'] ?? null;
        $sortField = $data['sort'] ?? 'id';
        $perPage = $data['per_page'] ?? 15;
        $sortDirection = $data['direction'] ?? 'desc';
        $bookingStatus = $data['booking_status'] ?? null;
        $bookingDate = $data['booking_date'] ?? null;
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;
        $fieldId = $data['field_id'] ?? null;

        $query = BookingParticipant::query()
            ->where('booking_participants.user_id', $userId)
            ->with([
                'booking' => function ($query) {
                    $query->select([
                        'id',
                        'booking_number',
                        'booking_date',
                        'start_time',
                        'end_time',
                        'booking_status',
                        'field_id',
                    ])->with(['field:id,name']);
                },
            ])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('booking', function ($bookingQuery) use ($search) {
                    $bookingQuery->where('booking_number', 'like', "%{$search}%")
                        ->orWhereHas('field', function ($fieldQuery) use ($search) {
                            $fieldQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($bookingStatus, function ($query) use ($bookingStatus) {
                $query->whereHas('booking', function ($q) use ($bookingStatus) {
                    $q->where('booking_status', $bookingStatus);
                });
            })
            ->when($bookingDate, function ($query) use ($bookingDate) {
                $query->whereHas('booking', function ($q) use ($bookingDate) {
                    $q->whereDate('booking_date', $bookingDate);
                });
            })
            ->when($fieldId, function ($query) use ($fieldId) {
                $query->whereHas('booking', function ($q) use ($fieldId) {
                    $q->where('field_id', $fieldId);
                });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereHas('booking', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('booking_date', [$startDate, $endDate]);
                });
            });

        $query->join('bookings', 'booking_participants.booking_id', '=', 'bookings.id');

        $query = $this->applySorting($query, $sortField, $sortDirection);

        $paginatedParticipations = $query->paginate($perPage);

        $paginatedParticipations->getCollection()->transform(function ($participation) use ($userId) {
            $booking = $participation->booking;

            $statistics = PlayerStatistic::where('user_id', $userId)
                ->where('booking_id', $booking->id)
                ->with('statistic:id,name,icon,color')
                ->get()
                ->mapWithKeys(fn ($stat) => [
                    $stat->statistic->name => [
                        'value' => $stat->count,
                        'icon' => $stat->statistic->icon,
                        'color' => $stat->statistic->color,
                    ],
                ]);

            $rating = PlayerRating::where('user_id', $userId)
                ->where('booking_id', $booking->id)
                ->first();

            return [
                'id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'booking_date' => $booking->booking_date->format('d/m/Y'),
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'booking_status' => $booking->booking_status,
                'field_name' => $booking->field->name ?? 'Campo não informado',
                'statistics' => $statistics->toArray(),
                'rating' => $rating ? [
                    'overall' => $rating->rating,
                    'technical' => $rating->technical_rating,
                    'tactical' => $rating->tactical_rating,
                    'physical' => $rating->physical_rating,
                    'mental' => $rating->mental_rating,
                    'teamwork' => $rating->teamwork_rating,
                    'comment' => $rating->comment,
                ] : null,
                'participation_id' => $participation->id,
            ];
        });

        return $paginatedParticipations;
    }
}
