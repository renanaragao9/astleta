<?php

namespace App\Nova\Filters\Expense;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\DateFilter;

class ExpenseDueDateToFilter extends DateFilter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->whereDate('expenses.due_date', '<=', $value);
    }

    public function name(): string
    {
        return 'Vencimento (Até)';
    }
}
