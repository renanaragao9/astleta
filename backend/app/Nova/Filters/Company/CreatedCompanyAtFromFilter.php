<?php

namespace App\Nova\Filters\Company;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\DateFilter;

class CreatedCompanyAtFromFilter extends DateFilter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->whereDate('created_at', '>=', $value);
    }

    public function name()
    {
        return 'Data de Criação (De)';
    }
}
