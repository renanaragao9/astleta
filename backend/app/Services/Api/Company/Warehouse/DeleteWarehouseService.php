<?php

namespace App\Services\Api\Company\Warehouse;

use App\Models\Warehouse;
use App\Services\Api\Company\Global\BaseService;

class DeleteWarehouseService extends BaseService
{
    public function run(Warehouse $warehouse): void
    {
        $this->getCompany();

        $warehouse->delete();
    }
}