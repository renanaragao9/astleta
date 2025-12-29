<?php

namespace App\Http\Resources\Athlete;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingAthleteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bookingNumber' => $this->booking_number,
            'bookingDate' => $this->booking_date->format('d/m/Y'),
            'startTime' => $this->formatTime($this->start_time),
            'endTime' => $this->formatTime($this->end_time),
            'durationMinutes' => $this->formatDuration($this->duration_minutes),
            'totalAmount' => $this->total_amount,
            'bookingStatus' => $this->booking_status,
            'createdAt' => $this->created_at->format('d/m/Y H:i:s'),
            'isExtraHour' => $this->is_extra_hour,

            'field' => $this->whenLoaded('field', fn () => [
                'id' => $this->field->id,
                'name' => $this->field->name,
                'pricePerHour' => $this->field->price_per_hour,
                'extraHourPrice' => $this->field->extra_hour_price,
            ]),

            'company' => $this->whenLoaded('field', fn () => $this->field && $this->field->relationLoaded('company') ? [
                'name' => $this->field->company->name,
                'address' => $this->field->company->relationLoaded('addresses')
                    ? ($this->field->company->addresses->first()?->getFullAddressAttribute() ?? null)
                    : null,
            ] : null),

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'phone' => $this->user->phone,
            ]),

            'coupon' => $this->whenLoaded('coupon', fn () => [
                'id' => $this->coupon->id,
                'code' => $this->coupon->code,
                'discountAmount' => $this->coupon->discount_amount,
                'discountType' => $this->coupon->discount_type,
            ]),

            'paymentForm' => $this->whenLoaded('paymentForm', fn () => [
                'id' => $this->paymentForm->id,
                'name' => $this->paymentForm->name,
                'type' => $this->paymentForm->type,
            ]),
        ];
    }

    private function formatTime($time): string
    {
        if ($time instanceof \Carbon\Carbon) {
            return $time->format('H:i');
        }

        return is_string($time) ? substr($time, 0, 5) : $time;
    }

    private function formatDuration($minutes): string
    {
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return match (true) {
            $hours === 0 => "{$remainingMinutes} minutos",
            $hours === 1 && $remainingMinutes === 0 => '1 hora',
            $hours === 1 => "1 hora e {$remainingMinutes} minutos",
            $remainingMinutes === 0 => "{$hours} horas",
            default => "{$hours} horas e {$remainingMinutes} minutos",
        };
    }
}
