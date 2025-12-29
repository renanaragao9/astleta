<?php

namespace App\Nova\Filters\Booking;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class BookingExtraHourFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('bookings.is_extra_hour', $value);
    }

    public function options(Request $request): array
    {
        return [
            'Sim' => 1,
            'Não' => 0,
        ];
    }

    public function name(): string
    {
        return 'Hora Extra';
    }
}
