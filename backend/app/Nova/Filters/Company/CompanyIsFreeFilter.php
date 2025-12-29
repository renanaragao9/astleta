<?php

namespace App\Nova\Filters\Company;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class CompanyIsFreeFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('is_free', $value);
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
        return 'É Gratuito';
    }
}
