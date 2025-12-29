<?php

namespace App\Nova\Filters\Booking;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class BookingStatusFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('bookings.booking_status', $value);
    }

    public function options(Request $request): array
    {
        return [
            'Pendente' => 'pendente',
            'Confirmado' => 'confirmado',
            'Cancelado' => 'cancelado',
            'Concluído' => 'concluido',
        ];
    }

    public function name(): string
    {
        return 'Status da Reserva';
    }
}
