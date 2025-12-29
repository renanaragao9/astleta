<?php

namespace App\Services\Api\Company\TabItem;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Tab;
use App\Models\TabItem;
use Illuminate\Support\Facades\DB;

class StoreTabItemService extends BaseService
{
    public function run(array $data): TabItem
    {
        return DB::transaction(function () use ($data) {
            $company = $this->getCompany();

            if (!$company) {
                throw new \Exception(message: 'Empresa não encontrada.');
            }

            $tab = Tab::where('id', $data['tab_id'])
                ->where('company_id', $company->id)
                ->first();

            $tabItem = TabItem::create($data);



            $tabItem->load([
                'tab',
                'product',
            ]);

            $this->updateTabTotal($tab);
            $this->reduceStock($tabItem);

            return $tabItem;
        });
    }

    private function updateTabTotal(Tab $tab): void
    {
        $totalAmount = $tab->tabItems()->sum('total');
        $tab->update(['total_amount' => $totalAmount]);
    }

    private function reduceStock(TabItem $tabItem): void
    {
        $availableStocks = Stock::where('product_id', $tabItem->product_id)
            ->where('company_id', $tabItem->tab->company_id)
            ->where('is_available_use', true)
            ->where('is_sale', false)
            ->limit($tabItem->quantity)
            ->get();

        $availableStocks->each(function ($stock) use ($tabItem) {
            $stock->update(['is_sale' => true]);

            StockMovement::create([
                'type' => 'saida',
                'status' => 'concluido',
                'stock_id' => $stock->id,
                'movimentable_type' => TabItem::class,
                'movimentable_id' => $tabItem->id,
            ]);
        });
    }
}
