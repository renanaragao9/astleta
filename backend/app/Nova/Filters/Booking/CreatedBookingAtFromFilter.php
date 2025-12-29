<?php

namespace App\Nova\Filters\Booking;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\DateFilter;

class CreatedBookingAtFromFilter extends DateFilter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->whereDate('bookings.created_at', '>=', $value);
    }

    public function name(): string
    {
        return 'Data de Criação (De)';
    }
}
