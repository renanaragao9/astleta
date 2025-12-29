<?php

namespace App\Services\Api\Company\Product;

use App\Models\Product;
use App\Services\Api\Company\Global\BaseService;

class ViewProductService extends BaseService
{
    public function run(int $id): ?Product
    {
        $company = $this->getCompany();

        $product = Product::where('id', $id)
            ->where('company_id', $company->id)
            ->with([
                'productType',
                'company',
            ])
            ->first();

        return $product;
    }
}
