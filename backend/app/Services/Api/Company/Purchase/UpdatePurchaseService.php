<?php

namespace App\Services\Api\Company\Purchase;

use App\Models\Purchase;
use App\Services\Api\Company\Global\BaseService;
use Illuminate\Validation\ValidationException;

class UpdatePurchaseService extends BaseService
{
    public function run(Purchase $purchase, array $data): Purchase
    {
        if ($purchase->status === 'cancelado') {
            throw ValidationException::withMessages([
                'error' => 'Não é possível editar uma compra cancelada.'
            ]);
        }

        $purchase->update($data);

        return $purchase->fresh();
    }
}