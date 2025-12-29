<?php

namespace App\Nova\Filters\User;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\DateFilter;

class CreatedUserAtFromFilter extends DateFilter
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
