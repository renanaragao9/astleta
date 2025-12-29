<?php

namespace App\Services\Api\Company\Supplier;

use App\Models\Supplier;
use App\Services\Api\Company\Global\BaseService;

class UpdateSupplierService extends BaseService
{
    public function run(Supplier $supplier, array $data): Supplier
    {
        $company = $this->getCompany();

        $data['company_id'] = $company->id;

        $supplier->update($data);

        return $supplier->refresh();
    }
}