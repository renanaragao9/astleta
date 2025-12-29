<?php

namespace App\Services\Api\Company\Booking;

use App\Models\Booking;
use App\Models\CompanyTransfer;
use App\Models\Coupon;
use App\Models\Field;
use App\Models\User;
use App\Services\Api\Company\Global\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StoreBookingService extends BaseService
{
    public function run(array $data): Booking
    {
        $this->validateTimeAvailability($data);

        $company = $this->getCompany();

        if ($company->status !== 'aprovado') {
            throw ValidationException::withMessages([
                'error' => 'A empresa está inadimplente. Não é possível criar reservas para uma empresa inadimplente.',
            ]);
        }

        $field = Field::where('id', $data['field_id'])
            ->where('company_id', $company->id)
            ->firstOrFail();

        $calculatedData = $this->calculateBookingData($field, $data);

        if (! empty($data['coupon_id'])) {
            $calculatedData = $this->applyCoupon($calculatedData, (int) $data['coupon_id']);
        }

        $calculatedData['booking_number'] = $this->generateBookingNumber();

        if (isset($data['user_phone'])) {
            $user = User::where('phone', $data['user_phone'])->firstOrFail();
            $calculatedData['user_id'] = $user->id;
        } else {
            $calculatedData['user_id'] = $this->getUserId();
        }

        $calculatedData['booking_status'] ??= 'pendente';
        $calculatedData['public_id'] = Str::uuid();

        return DB::transaction(function () use ($calculatedData, $company) {
            $booking = Booking::create($calculatedData);

            $feePercentage = config('system.fee_percentage', 3);
            CompanyTransfer::create([
                'booking_id' => $booking->id,
                'company_id' => $company->id,
                'fee_amount' => round($calculatedData['total_amount'] * ($feePercentage / 100), 2),
                'is_free' => $company->is_free,
            ]);

            return $booking->load([
                'field:id,name,company_id,price_per_hour,extra_hour_price',
                'field.fieldType:id,name',
                'field.fieldSurface:id,name',
                'field.fieldSize:id,name',
                'user:id,name,email',
                'coupon:id,code,discount_amount,discount_type',
                'paymentForm:id,name,type',
            ]);
        });
    }

    private function validateTimeAvailability(array $data): void
    {
        $conflictExists = Booking::query()
            ->where('field_id', $data['field_id'])
            ->where('booking_date', $data['booking_date'])
            ->where('booking_status', '!=', 'cancelado')
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->exists();

        if ($conflictExists) {
            throw ValidationException::withMessages([
                'time' => 'Horário não disponível. Já existe uma reserva conflitante.',
            ]);
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
            $endTime = $endTime->addMinutes(30);
        }

        $totalPrice = $basePrice + $extraHourPrice;

        return array_merge($data, [
            'duration_minutes' => $durationMinutes,
            'base_price' => round($basePrice, 2),
            'extra_hour_price' => round($extraHourPrice, 2),
            'discount_amount' => 0,
            'total_amount' => round($totalPrice, 2),
            'end_time' => $endTime->format('H:i:s'),
        ]);
    }

    private function applyCoupon(array $data, int $couponId): array
    {
        $coupon = Coupon::where('id', $couponId)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->firstOrFail();

        $discountAmount = $coupon->discount_type === 'percentage'
            ? ($data['base_price'] * $coupon->discount_amount) / 100
            : $coupon->discount_amount;

        $discountAmount = min($discountAmount, $data['base_price']);

        $data['discount_amount'] = round($discountAmount, 2);
        $data['total_amount'] = round($data['base_price'] - $discountAmount, 2);

        return $data;
    }

    private function generateBookingNumber(): string
    {
        $prefix = 'RES';
        $timestamp = now()->format('YmdHis');
        $random = rand(100, 999);

        return "{$prefix}{$timestamp}{$random}";
    }
}
