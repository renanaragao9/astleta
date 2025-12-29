<?php

namespace App\Nova\Filters\Company;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class CompanyTypeFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('type', $value);
    }

    public function options(Request $request): array
    {
        return [
            'Pessoa Jurídica' => 'pj',
            'Pessoa Física' => 'fisica',
        ];
    }

    public function name(): string
    {
        return 'Tipo';
    }
}
