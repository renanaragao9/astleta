<?php

namespace App\Services\Api\Company\Product;

use App\Models\Product;
use App\Services\Api\Company\Global\BaseService;

class UpdateProductService extends BaseService
{
    public function run(Product $product, array $data): Product
    {
        $company = $this->getCompany();

        $data['company_id'] = $company->id;

        $product->update($data);

        return $product->refresh();
    }
}
