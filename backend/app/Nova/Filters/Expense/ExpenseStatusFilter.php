<?php

namespace App\Nova\Filters\Expense;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class ExpenseStatusFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('expenses.is_paid', $value);
    }

    public function options(Request $request): array
    {
        return [
            'Pago' => true,
            'Pendente' => false,
        ];
    }

    public function name(): string
    {
        return 'Status de Pagamento';
    }
}
