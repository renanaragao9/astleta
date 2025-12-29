<?php

namespace App\Nova\Filters\Booking;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\DateFilter;

class BookingDateToFilter extends DateFilter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->whereDate('bookings.booking_date', '<=', $value);
    }

    public function name(): string
    {
        return 'Data da Reserva (Até)';
    }
}
