<?php

namespace App\Services\Api\Company\Tab;

use App\Models\Tab;
use App\Services\Api\Company\Global\BaseService;
use Illuminate\Validation\ValidationException;

class StoreTabService extends BaseService
{
    public function run(array $data): Tab
    {
        $company = $this->getCompany();

        if ($company->status !== 'aprovado') {
            throw ValidationException::withMessages([
                'error' => 'A empresa está inadimplente. Não é possível criar reservas para uma empresa inadimplente.',
            ]);
        }

        $data['company_id'] = $company->id;
        $data['status'] ??= 'aberto';
        $data['total_amount'] ??= 0.00;
        $data['opened_at'] ??= now();

        $tab = Tab::create($data);

        $tab->load([
            'company',
            'paymentForm',
            'tabItems.product',
        ]);

        return $tab;
    }
}
