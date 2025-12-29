<?php

namespace App\Services\Api\Athlete\BookingStatistic;

use App\Models\Booking;
use App\Models\PlayerStatistic;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class IndexBookingStatisticService extends BaseService
{
    public function run(Booking $booking): Collection
    {
        $userId = $this->getUserId();

        if ($booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva não encontrada ou você não tem permissão para acessá-la.']);
        }

        return PlayerStatistic::where('booking_id', $booking->id)
            ->with([
                'user:id,name,email,image_path',
                'statistic:id,name',
                'bookingParticipant:id,name,user_id',
                'bookingParticipant.user:id,name,email,image_path',
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
