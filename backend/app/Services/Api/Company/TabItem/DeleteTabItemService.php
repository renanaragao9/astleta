<?php

namespace App\Services\Api\Company\TabItem;

use App\Models\TabItem;
use App\Services\Api\Company\TabItem\RevertStockService;
use Illuminate\Support\Facades\DB;

class DeleteTabItemService extends BaseService
{
    public function run(TabItem $tabItem): void
    {
        DB::transaction(function () use ($tabItem) {
            $company = $this->getCompany();

            if (!$company) {
                throw new \Exception('Empresa não encontrada.');
            }

            $tab = $tabItem->tab;

            $revertStockService = new RevertStockService();
            $revertStockService->revertForTabItem($tabItem);

            $tabItem->delete();

            $this->updateTabTotal($tab);
        });
    }

    private function updateTabTotal($tab): void
    {
        $totalAmount = $tab->tabItems()->sum('total');
        $tab->update(['total_amount' => $totalAmount]);
    }
}
