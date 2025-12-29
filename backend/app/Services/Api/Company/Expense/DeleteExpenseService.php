<?php

namespace App\Services\Api\Company\Expense;

use App\Models\Expense;
use App\Services\Api\Company\Global\BaseService;

class DeleteExpenseService extends BaseService
{
    public function run(Expense $expense): void
    {
        $this->getCompany();

        $expense->delete();
    }
}
