<?php

namespace App\Services\Api\Company\Stock;

use App\Models\Stock;
use App\Services\Api\Company\Global\BaseService;

class StoreStockService extends BaseService
{
    public function run(array $data): Stock
    {
        $company = $this->getCompany();

        $data['company_id'] = $company->id;

        return Stock::create($data);
    }
}