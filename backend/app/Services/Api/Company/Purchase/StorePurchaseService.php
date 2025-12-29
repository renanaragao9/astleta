<?php

namespace App\Services\Api\Company\Purchase;

use App\Models\Purchase;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use App\Services\Api\Company\Global\BaseService;

class StorePurchaseService extends BaseService
{
    public function run(array $data): Purchase
    {
        $company = $this->getCompany();

        return DB::transaction(function () use ($data, $company) {
            $data['company_id'] = $company->id;
            $data['status'] = 'concluido';
            $items = $data['items'];
            unset($data['items']);

            $purchase = Purchase::create($data);

            foreach ($items as $item) {
                for ($i = 0; $i < $item['quantity']; $i++) {
                    $stock = Stock::create([
                        'product_id' => $item['product_id'],
                        'warehouse_id' => $item['warehouse_id'],
                        'is_available_use' => true,
                        'is_sale' => false,
                        'status' => 'concluido',
                        'company_id' => $company->id,
                    ]);

                    StockMovement::create([
                        'type' => 'entrada',
                        'status' => 'concluido',
                        'stock_id' => $stock->id,
                        'movimentable_type' => Purchase::class,
                        'movimentable_id' => $purchase->id,
                    ]);
                }
            }

            return $purchase;
        });
    }
}