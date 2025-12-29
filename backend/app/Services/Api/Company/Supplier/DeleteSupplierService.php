<?php

namespace App\Services\Api\Company\Supplier;

use App\Models\Supplier;
use App\Services\Api\Company\Global\BaseService;

class DeleteSupplierService extends BaseService
{
    public function run(Supplier $supplier): void
    {
        $this->getCompany();

        $supplier->delete();
    }
}