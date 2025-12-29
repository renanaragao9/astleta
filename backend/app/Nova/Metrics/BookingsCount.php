<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class BookingsCount extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, \App\Models\Booking::class);
    }

    public function ranges()
    {
        return [
            30 => '30 Dias',
            60 => '60 Dias',
            365 => '365 Dias',
            'TODAY' => 'Hoje',
            'MTD' => 'Mês Atual',
            'QTD' => 'Trimestre Atual',
            'YTD' => 'Ano Atual',
        ];
    }

    public function uriKey()
    {
        return 'bookings-count';
    }

    public function name()
    {
        return 'Total de Reservas';
    }
}
