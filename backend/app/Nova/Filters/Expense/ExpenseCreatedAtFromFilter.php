<?php

namespace App\Nova\Filters\Expense;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\DateFilter;

class ExpenseCreatedAtFromFilter extends DateFilter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->whereDate('expenses.created_at', '>=', $value);
    }

    public function name(): string
    {
        return 'Criado Em (De)';
    }
}
