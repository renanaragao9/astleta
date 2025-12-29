<?php

namespace App\Services\Api\Company\Supplier;

use App\Models\Supplier;
use App\Services\Api\Company\Global\BaseService;
use Illuminate\Database\Eloquent\Collection;

class SelectSupplierService extends BaseService
{
    public function run(): Collection
    {
        $company = $this->getCompany();

        return Supplier::query()
            ->where('company_id', $company->id)
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }
}