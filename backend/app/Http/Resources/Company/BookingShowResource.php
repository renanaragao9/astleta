<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bookingNumber' => $this->booking_number,
            'bookingDate' => $this->booking_date?->format('d/m/Y'),
            'startTime' => $this->start_time ? $this->formatTime($this->start_time) : null,
            'endTime' => $this->end_time ? $this->formatTime($this->end_time) : null,
            'durationMinutes' => $this->duration_minutes,
            'basePrice' => $this->base_price,
            'discountAmount' => $this->discount_amount,
            'totalAmount' => $this->total_amount,
            'notes' => $this->notes,
            'cancellationReason' => $this->cancellation_reason,
            'bookingStatus' => $this->booking_status,
            'asaasPaymentId' => $this->asaas_payment_id,
            'asaasCustomerId' => $this->asaas_customer_id,
            'isExtraHour' => $this->is_extra_hour,
            'createdAt' => $this->created_at?->format('d/m/Y H:i:s'),
            'updatedAt' => $this->updated_at?->format('d/m/Y H:i:s'),

            'field' => $this->whenLoaded('field', function () {
                return [
                    'name' => $this->field->name,
                ];
            }),

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'phone' => $this->user->phone,
                ];
            }),

            'coupon' => $this->whenLoaded('coupon', function () {
                return [
                    'id' => $this->coupon->id,
                    'code' => $this->coupon->code,
                    'discountAmount' => $this->coupon->discount_amount,
                    'discountType' => $this->coupon->discount_type,
                ];
            }),

            'paymentForm' => $this->whenLoaded('paymentForm', function () {
                return [
                    'id' => $this->paymentForm->id,
                    'name' => $this->paymentForm->name,
                    'type' => $this->paymentForm->type,
                ];
            }),

            'formattedBasePrice' => 'R$ '.number_format((float) $this->base_price, 2, ',', '.'),
            'formattedDiscountAmount' => 'R$ '.number_format((float) $this->discount_amount, 2, ',', '.'),
            'formattedTotalAmount' => 'R$ '.number_format((float) $this->total_amount, 2, ',', '.'),
            'formattedBookingDate' => $this->booking_date?->format('d/m/Y'),
            'formattedDuration' => $this->formatDuration($this->duration_minutes),
            'statusLabel' => $this->getStatusLabel(),
            'paymentTypeLabel' => $this->getPaymentTypeLabel(),
        ];
    }

    private function formatTime($time): string
    {
        if ($time instanceof \Carbon\Carbon) {
            return $time->format('H:i');
        }

        return is_string($time) ? substr($time, 0, 5) : $time;
    }

    private function formatDuration(?int $minutes): string
    {
        if (! $minutes) {
            return '0min';
        }

        $hours = intval($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0 && $remainingMinutes > 0) {
            return "{$hours}h {$remainingMinutes}min";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$remainingMinutes}min";
        }
    }

    private function getStatusLabel(): string
    {
        return match ($this->booking_status) {
            'pendente' => 'Pendente',
            'confirmado' => 'Confirmado',
            'cancelado' => 'Cancelado',
            'concluido' => 'Concluído',
            default => 'Desconhecido',
        };
    }

    private function getPaymentTypeLabel(): string
    {
        return match ($this->payment_type) {
            'online' => 'Online',
            'presencial' => 'Presencial',
            default => 'Não informado',
        };
    }
}
