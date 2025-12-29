<?php

namespace App\Services\Api\Company\Expense;

use App\Models\Expense;
use App\Services\Api\Company\Global\BaseService;
use App\Traits\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexExpenseService extends BaseService
{
    use Sortable;

    public function run(array $data): LengthAwarePaginator
    {
        $company = $this->getCompany();
        $search = $data['search'] ?? null;
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'asc';
        $perPage = $data['per_page'] ?? 15;
        $page = $data['page'] ?? 1;
        $isPaid = isset($data['is_paid']) ? $data['is_paid'] : null;
        $type = $data['type'] ?? null;
        $expenseTypeId = $data['expense_type_id'] ?? null;
        $startDueDate = $data['start_due_date'] ?? null;
        $endDueDate = $data['end_due_date'] ?? null;

        $query = Expense::where('company_id', $company->id)
            ->with([
                'expenseType',
                'company',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($isPaid, function ($query) use ($isPaid) {
                $query->where('is_paid', filter_var($isPaid, FILTER_VALIDATE_BOOLEAN));
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($expenseTypeId, function ($query) use ($expenseTypeId) {
                $query->where('expense_type_id', $expenseTypeId);
            })
            ->when($startDueDate, function ($query) use ($startDueDate) {
                $query->where('due_date', '>=', $startDueDate);
            })
            ->when($endDueDate, function ($query) use ($endDueDate) {
                $query->where('due_date', '<=', $endDueDate);
            });

        $query = $this->applySorting($query, $sort, $direction);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
