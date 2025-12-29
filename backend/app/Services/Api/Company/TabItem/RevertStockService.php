<?php

namespace App\Services\Api\Company\TabItem;

use App\Models\StockMovement;
use App\Models\Tab;
use App\Models\TabItem;
use App\Services\Api\Company\Global\BaseService;

class RevertStockService extends BaseService
{
    public function revertForTabItem(TabItem $tabItem): void
    {
        $stockMovements = StockMovement::where('movimentable_type', TabItem::class)
            ->where('movimentable_id', $tabItem->id)
            ->where('status', 'concluido')
            ->get();

        $stockMovements->each(function ($movement) {
            $stock = $movement->stock;
            $stock->update(['is_sale' => false]);
            $movement->update(['status' => 'cancelado']);
        });
    }

    public function revertForTab(Tab $tab): void
    {
        $tab->load('tabItems');

        $tab->tabItems->each(function ($tabItem) {
            $this->revertForTabItem($tabItem);
        });
    }
}