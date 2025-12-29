<?php

namespace App\Services\Api\Company\Product;

use App\Models\Product;
use App\Services\Api\Company\Global\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SelectProductService extends BaseService
{
    public function run(): Collection
    {
        $company = $this->getCompany();

        return Product::query()
            ->where('company_id', $company->id)
            ->where('is_active', true)
            ->select([
                'id',
                DB::raw("CONCAT(name, ' (Qtd. ', (SELECT COUNT(*) FROM stocks WHERE stocks.product_id = products.id AND stocks.is_available_use = true AND stocks.status = 'concluido' AND stocks.is_sale = false), ')') AS name"),
                'price'
            ])
            ->orderBy('name')
            ->get();
    }
}
