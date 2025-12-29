<?php

namespace App\Services\Api\Company\Purchase;

use App\Models\Purchase;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class DeletePurchaseService
{
    public function run(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            $purchase->stockMovements()->update(['status' => 'cancelado']);

            $stockIds = $purchase->stockMovements()->pluck('stock_id');
            Stock::whereIn('id', $stockIds)->update(['status' => 'cancelado']);

            $purchase->update(['status' => 'cancelado']);
        });
    }
}