<?php

namespace App\Nova\Filters\Company;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class CompanyStatusFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('status', $value);
    }

    public function options(Request $request): array
    {
        return [
            'Rejeitado' => 'rejeitado',
            'Aprovado' => 'aprovado',
            'Pendente' => 'pendente',
        ];
    }

    public function name(): string
    {
        return 'Status';
    }
}
