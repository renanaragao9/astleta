<?php

namespace App\Services\Api\Company\Warehouse;

use App\Models\Warehouse;
use App\Services\Api\Company\Global\BaseService;
use Illuminate\Database\Eloquent\Collection;

class SelectWarehouseService extends BaseService
{
    public function run(): Collection
    {
        $company = $this->getCompany();

        return Warehouse::query()
            ->where('company_id', $company->id)
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }
}