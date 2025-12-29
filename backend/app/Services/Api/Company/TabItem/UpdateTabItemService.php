<?php

namespace App\Services\Api\Company\TabItem;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Tab;
use App\Models\TabItem;
use App\Services\Api\Company\TabItem\RevertStockService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class UpdateTabItemService extends BaseService
{
    public function run(TabItem $tabItem, array $data): TabItem
    {
        $company = $this->getCompany();

        if (!$company) {
            throw new ModelNotFoundException('Empresa não encontrada.');
        }

        $newTabId = $data['tab_id'] ?? null;
        $oldTab = $tabItem->tab;
        $oldQuantity = $tabItem->quantity;

        return DB::transaction(function () use ($tabItem, $data, $company, $newTabId, $oldTab, $oldQuantity) {
            if ($newTabId && $newTabId !== $oldTab->id) {
                Tab::where('id', $newTabId)
                    ->where('company_id', $company->id)
                    ->firstOrFail();
            }
            if (isset($data['quantity']) && $data['quantity'] != $oldQuantity) {
                $revertStockService = new RevertStockService();
                $revertStockService->revertForTabItem($tabItem);
            }

            $tabItem->update($data);

            if (isset($data['quantity']) && $data['quantity'] != $oldQuantity) {
                $this->reduceStock($tabItem);
            }

            $this->updateTabTotal($oldTab);

            if ($newTabId && $newTabId !== $oldTab->id) {
                $this->updateTabTotal($tabItem->tab);
            }

            return $tabItem->refresh()->load(['tab', 'product']);
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
