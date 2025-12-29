<?php

namespace App\Services\Api\Company\Booking;

use App\Models\Field;
use App\Services\Api\Company\Global\BaseService;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class CalculatePriceBookingService extends BaseService
{
    public function run(array $data): array
    {
        $company = $this->getCompany();

        $field = Field::where('id', $data['field_id'])
            ->where('company_id', $company->id)
            ->first();

        if (! $field) {
            throw ValidationException::withMessages([
                'error' => 'Campo não encontrado ou não pertence à sua empresa.',
            ]);
        }

        $start = Carbon::parse($data['start_time']);
        $end = Carbon::parse($data['end_time']);

        $durationMinutes = $start->diffInMinutes($end);
        $durationHours = $durationMinutes / 60;

        $basePrice = $field->price_per_hour * $durationHours;
        $extraHourPrice = 0;

        if (isset($data['include_extra_hour']) && $data['include_extra_hour'] && $field->is_allows_extra_hour) {
            $extraHourPrice = $field->extra_hour_price ?? 0;
            $durationMinutes += 30;
        }

        $totalPrice = $basePrice + $extraHourPrice;

        return [
            'duration_minutes' => $durationMinutes,
            'duration_hours' => round($durationMinutes / 60, 2),
            'base_price' => round($basePrice, 2),
            'extra_hour_price' => round($extraHourPrice, 2),
            'total_price' => round($totalPrice, 2),
        ];
    }
}
