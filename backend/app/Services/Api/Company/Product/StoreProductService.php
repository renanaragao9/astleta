<?php

namespace App\Services\Api\Company\Product;

use App\Models\Product;
use App\Services\Api\Company\Global\BaseService;

class StoreProductService extends BaseService
{
    public function run(array $data): Product
    {
        $company = $this->getCompany();

        $data['company_id'] = $company->id;

        return Product::create($data);
    }
}
