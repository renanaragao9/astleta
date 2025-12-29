<?php

namespace App\Nova\Filters\Tab;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class TabStatusFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('tabs.status', $value);
    }

    public function options(Request $request): array
    {
        return [
            'Aberto' => 'aberto',
            'Pago' => 'pago',
            'Cancelado' => 'cancelado',
        ];
    }

    public function name(): string
    {
        return 'Status da Comanda';
    }
}
