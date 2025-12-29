<?php

namespace App\Nova\Filters\Tab;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\DateFilter;

class TabOpenedAtFromFilter extends DateFilter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->whereDate('tabs.opened_at', '>=', $value);
    }

    public function name(): string
    {
        return 'Aberto Em (De)';
    }
}
