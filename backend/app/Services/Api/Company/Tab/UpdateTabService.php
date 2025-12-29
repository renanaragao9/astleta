<?php

namespace App\Services\Api\Company\Tab;

use App\Models\Tab;
use App\Services\Api\Company\Global\BaseService;
use App\Services\Api\Company\TabItem\RevertStockService;
use Illuminate\Validation\ValidationException;

class UpdateTabService extends BaseService
{
    public function run(Tab $tab, array $data): Tab
    {
        $this->getCompany();

        if (!empty($data['closed_at'])) {
            $data['closed_at'] = now()->format('Y-m-d H:i:s');
        }

        if (isset($data['status']) && $data['status'] === 'cancelado' && $tab->status === 'pago') {
            throw ValidationException::withMessages([
                'error' => 'Não é possível cancelar uma comanda que já foi paga.',
            ]);
        }

        $tab->update($data);

        if (isset($data['status']) && $data['status'] === 'cancelado') {
            $revertStockService = new RevertStockService();
            $revertStockService->revertForTab($tab);
        }

        return $tab->refresh()->load([
            'company',
            'paymentForm',
            'tabItems.product',
        ]);
    }
}
