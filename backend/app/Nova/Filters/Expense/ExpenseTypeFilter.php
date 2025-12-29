<?php

namespace App\Nova\Filters\Expense;

use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class ExpenseTypeFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('expenses.expense_type_id', $value);
    }

    public function options(Request $request): array
    {
        $expenseTypes = ExpenseType::select('id', 'name')->orderBy('name')->get();

        $options = [];
        foreach ($expenseTypes as $expenseType) {
            $options[$expenseType->name] = $expenseType->id;
        }

        return $options;
    }

    public function name(): string
    {
        return 'Categoria';
    }
}
