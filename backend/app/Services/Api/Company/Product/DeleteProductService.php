<?php

namespace App\Services\Api\Company\Product;

use App\Models\Product;
use App\Services\Api\Company\Global\BaseService;

class DeleteProductService extends BaseService
{
    public function run(Product $product): void
    {
        $this->getCompany();

        $product->delete();
    }
}
