<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'cancellation_reason' => $this->cancellation_reason,
            'createdAt' => $this->created_at->format('d/m/Y H:i:s'),
            'isExtraHour' => $this->is_extra_hour,
            'notes' => $this->notes,

            'field' => $this->whenLoaded('field', fn() => [
                'id' => $this->field->id,
                'name' => $this->field->name,
                'pricePerHour' => $this->field->price_per_hour,
                'extraHourPrice' => $this->field->extra_hour_price,
            ]),

            'user' => $this->whenLoaded('user', fn() => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'phone' => $this->user->phone,
            ]),

            'paymentForm' => $this->whenLoaded('paymentForm', fn() => [
                'id' => $this->paymentForm->id,
                'name' => $this->paymentForm->name,
                'type' => $this->paymentForm->type,
            ]),

            'coupon' => $this->whenLoaded('coupon', fn() => [
                'id' => $this->coupon->id,
                'code' => $this->coupon->code,
                'discountAmount' => $this->coupon->discount_amount,
                'discountType' => $this->coupon->discount_type,
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
