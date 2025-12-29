<?php

namespace App\Services\Api\Company\Stock;

use App\Models\Stock;
use App\Services\Api\Company\Global\BaseService;

class DeleteStockService extends BaseService
{
    public function run(Stock $stock): void
    {
        $this->getCompany();

        $stock->delete();
    }
}