<?php

namespace App\Nova\Filters\Booking;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class BookingPaymentTypeFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('bookings.payment_type', $value);
    }

    public function options(Request $request): array
    {
        return [
            'Online' => 'online',
            'Presencial' => 'presencial',
        ];
    }

    public function name(): string
    {
        return 'Tipo de Pagamento';
    }
}
