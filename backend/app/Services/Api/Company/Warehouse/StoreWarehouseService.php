<?php

namespace App\Services\Api\Company\Warehouse;

use App\Models\Warehouse;
use App\Services\Api\Company\Global\BaseService;

class StoreWarehouseService extends BaseService
{
    public function run(array $data): Warehouse
    {
        $company = $this->getCompany();

        $data['company_id'] = $company->id;

        return Warehouse::create($data);
    }
}