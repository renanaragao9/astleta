<?php

namespace App\Services\Api\Company\Tab;

use App\Models\Tab;
use App\Services\Api\Company\Global\BaseService;
use App\Services\Api\Company\TabItem\RevertStockService;
use Illuminate\Support\Facades\DB;

class DeleteTabService extends BaseService
{
    public function run(Tab $tab): void
    {
        DB::transaction(function () use ($tab) {
            $this->getCompany();

            $tab->load('tabItems');

            $revertStockService = new RevertStockService();
            $revertStockService->revertForTab($tab);

            $tab->status = 'cancelado';
            $tab->save();
        });
    }
}
