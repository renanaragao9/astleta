<?php

namespace App\Services\Api\Company\Booking;

use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Field;
use App\Services\Api\Company\Global\BaseService;
use Carbon\Carbon;

class UpdateBookingService extends BaseService
{
    public function run(Booking $booking, array $data): Booking
    {
        $company = $this->getCompany();

        if ($booking->booking_status === 'concluido') {
            throw new \Exception('Não é possível editar uma reserva já concluída.');
        }

        if (! empty($data['field_id']) && $data['field_id'] != $booking->field_id) {
            $field = Field::where('id', $data['field_id'])
                ->where('company_id', $company->id)
                ->first();

            if (! $field) {
                throw new \Exception('Campo não encontrado ou não pertence à sua empresa.');
            }
        }

        if ($this->hasTimeOrDateChanged($booking, $data)) {
            $this->validateTimeAvailability($data, $booking->id);
        }

        if ($this->shouldRecalculatePrices($booking, $data)) {
            $field = $booking->field;
            $calculatedData = $this->calculateBookingData($field, array_merge($booking->toArray(), $data));
            $data = array_merge($data, [
                'duration_minutes' => $calculatedData['duration_minutes'],
                'base_price' => $calculatedData['base_price'],
                'total_amount' => $calculatedData['total_amount'],
            ]);

            if (! empty($data['coupon_id'])) {
                $data = $this->applyCoupon($data, $data['coupon_id']);
            }
        }

        $booking->update($data);

        $booking->load([
            'field:id,name,company_id,price_per_hour,extra_hour_price',
            'field.fieldType:id,name',
            'field.fieldSurface:id,name',
            'field.fieldSize:id,name',
            'user:id,name,email',
            'coupon:id,code,discount_amount,discount_type',
            'paymentForm:id,name,type',
        ]);

        return $booking;
    }

    private function hasTimeOrDateChanged(Booking $booking, array $data): bool
    {
        $bookingDate = $booking->booking_date instanceof Carbon
            ? $booking->booking_date->format('Y-m-d')
            : $booking->booking_date;

        $startTime = $booking->start_time instanceof Carbon
            ? $booking->start_time->format('H:i')
            : $booking->start_time;

        $endTime = $booking->end_time instanceof Carbon
            ? $booking->end_time->format('H:i')
            : $booking->end_time;

        return (! empty($data['booking_date']) && $data['booking_date'] != $bookingDate) ||
            (! empty($data['start_time']) && $data['start_time'] != $startTime) ||
            (! empty($data['end_time']) && $data['end_time'] != $endTime);
    }

    private function shouldRecalculatePrices(Booking $booking, array $data): bool
    {
        return $this->hasTimeOrDateChanged($booking, $data) ||
            (! empty($data['field_id']) && $data['field_id'] != $booking->field_id) ||
            (isset($data['is_extra_hour']) && $data['is_extra_hour'] != $booking->is_extra_hour);
    }

    private function validateTimeAvailability(array $data, int $excludeBookingId): void
    {
        $existingBooking = Booking::where('field_id', $data['field_id'])
            ->where('booking_date', $data['booking_date'])
            ->where('id', '!=', $excludeBookingId)
            ->where('booking_status', '!=', 'cancelado')
            ->where(function ($query) use ($data) {
                $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                    ->orWhere(function ($q) use ($data) {
                        $q->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                    });
            })
            ->exists();

        if ($existingBooking) {
            throw new \Exception('Horário não disponível. Já existe uma reserva conflitante.');
        }
    }

    private function calculateBookingData(Field $field, array $data): array
    {
        $startTime = Carbon::parse($data['start_time']);
        $endTime = Carbon::parse($data['end_time']);

        $durationMinutes = $startTime->diffInMinutes($endTime);
        $durationHours = $durationMinutes / 60;

        $basePrice = $field->price_per_hour * $durationHours;
        $extraHourPrice = 0;

        if (! empty($data['is_extra_hour']) && $field->is_allows_extra_hour) {
            $extraHourPrice = $field->extra_hour_price ?? 0;
            $durationMinutes += 30;
        }

        $totalPrice = $basePrice + $extraHourPrice;

        return [
            'duration_minutes' => $durationMinutes,
            'base_price' => round($basePrice, 2),
            'extra_hour_price' => round($extraHourPrice, 2),
            'total_amount' => round($totalPrice, 2),
        ];
    }

    private function applyCoupon(array $data, int $couponId): array
    {
        $coupon = Coupon::where('id', $couponId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->first();

        if (! $coupon) {
            throw new \Exception('Cupom inválido ou expirado.');
        }

        $discountAmount = 0;

        if ($coupon->discount_type === 'percentage') {
            $discountAmount = ($data['base_price'] * $coupon->discount_amount) / 100;
        } else {
            $discountAmount = $coupon->discount_amount;
        }

        $discountAmount = min($discountAmount, $data['base_price']);

        $data['discount_amount'] = round($discountAmount, 2);
        $data['total_amount'] = round($data['base_price'] - $discountAmount, 2);

        return $data;
    }
}
