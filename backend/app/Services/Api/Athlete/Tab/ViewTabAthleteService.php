<?php

namespace App\Services\Api\Athlete\Tab;

use App\Models\Tab;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Validation\ValidationException;

class ViewTabAthleteService extends BaseService
{
    public function run(Tab $tab): ?Tab
    {
        $userId = $this->getUserId();

        if (! $userId || $tab->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Comanda não encontrada ou você não tem permissão para acessá-la.']);
        }

        return $tab->load([
            'user',
            'paymentForm',
            'tabItems.product',
        ]);
    }
}
