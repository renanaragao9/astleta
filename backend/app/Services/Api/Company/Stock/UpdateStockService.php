<?php

namespace App\Services\Api\Company\Stock;

use App\Models\Stock;
use App\Services\Api\Company\Global\BaseService;

class UpdateStockService extends BaseService
{
    public function run(Stock $stock, array $data): Stock
    {
        $company = $this->getCompany();

        $data['company_id'] = $company->id;

        $stock->update($data);

        return $stock->refresh();
    }
}