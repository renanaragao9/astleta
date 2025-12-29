<?php

namespace App\Services\Api\Company\Expense;

use App\Models\Expense;
use App\Services\Api\Company\Global\BaseService;

class UpdateExpenseService extends BaseService
{
    public function run(Expense $expense, array $data): Expense
    {
        $company = $this->getCompany();

        $data['company_id'] = $company->id;

        $expense->update($data);

        return $expense->refresh();
    }
}
