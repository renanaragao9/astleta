<?php

namespace App\Nova\Filters\Expense;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class ExpenseTypeValueFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('expenses.type', $value);
    }

    public function options(Request $request): array
    {
        return [
            'Entrada' => 'entrada',
            'Saída' => 'saida',
        ];
    }

    public function name(): string
    {
        return 'Tipo (Entrada/Saída)';
    }
}
