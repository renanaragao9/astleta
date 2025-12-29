<?php

namespace App\Services\Api\Company\Supplier;

use App\Models\Supplier;
use App\Services\Api\Company\Global\BaseService;

class StoreSupplierService extends BaseService
{
    public function run(array $data): Supplier
    {
        $company = $this->getCompany();

        $data['company_id'] = $company->id;

        return Supplier::create($data);
    }
}