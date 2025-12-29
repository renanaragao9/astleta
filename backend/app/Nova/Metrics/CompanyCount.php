<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class CompanyCount extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, \App\Models\Company::class);
    }

    public function ranges(): array
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

    public function uriKey(): string
    {
        return 'empresas-count';
    }

    public function name(): string
    {
        return 'Total de Empresas';
    }
}
