<?php

namespace App\Services\Api\Company\Expense;

use App\Models\Expense;
use App\Services\Api\Company\Global\BaseService;

class ViewService extends BaseService
{
    public function run(int $id): ?Expense
    {
        $company = $this->getCompany();

        $expense = Expense::where('id', $id)
            ->where('company_id', $company->id)
            ->with([
                'expenseType',
                'company',
            ])
            ->first();

        return $expense;
    }
}
