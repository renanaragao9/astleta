<?php

namespace App\Services\Api\Company\Warehouse;

use App\Models\Warehouse;
use App\Services\Api\Company\Global\BaseService;

class UpdateWarehouseService extends BaseService
{
    public function run(Warehouse $warehouse, array $data): Warehouse
    {
        $company = $this->getCompany();

        $data['company_id'] = $company->id;

        $warehouse->update($data);

        return $warehouse->refresh();
    }
}