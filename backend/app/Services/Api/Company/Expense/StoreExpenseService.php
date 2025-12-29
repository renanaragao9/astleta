<?php

namespace App\Services\Api\Company\Expense;

use App\Models\Expense;
use App\Services\Api\Company\Global\BaseService;

class StoreExpenseService extends BaseService
{
    public function run(array $data): Expense
    {
        $company = $this->getCompany();

        $data['company_id'] = $company->id;

        return Expense::create($data);
    }
}
