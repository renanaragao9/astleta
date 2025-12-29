<?php

namespace App\Nova\Filters\User;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\DateFilter;

class UserBirthDateToFilter extends DateFilter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->whereDate('date', '<=', $value);
    }

    public function name()
    {
        return 'Data de Nascimento (Até)';
    }
}
