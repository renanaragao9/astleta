<?php

namespace App\Services\Api\Athlete\Booking;

use App\Models\Booking;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Validation\ValidationException;

class ViewBookingAthleteService extends BaseService
{
    public function run(Booking $booking): Booking
    {
        $userId = $this->getUserId();

        if (! $userId || $booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva não encontrada ou você não tem permissão para acessá-la.']);
        }

        return $booking->load([
            'field:id,name,company_id,price_per_hour,extra_hour_price',
            'field.company:id,name',
            'field.company.addresses',
            'field.fieldType:id,name',
            'field.fieldSurface:id,name',
            'field.fieldSize:id,name',
            'user:id,name,email,phone',
            'coupon:id,code,discount_amount,discount_type',
            'paymentForm:id,name,type',
        ]);
    }
}
