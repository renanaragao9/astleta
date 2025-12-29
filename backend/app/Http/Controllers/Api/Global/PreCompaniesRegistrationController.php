<?php

namespace App\Http\Controllers\Api\Global;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Global\StorePreCompaniesRegistrationRequest;
use App\Services\Api\Global\PreCompaniesRegistration\StorePreCompaniesRegistrationService;
use Illuminate\Http\JsonResponse;

class PreCompaniesRegistrationController extends BaseController
{
    public function store(
        StorePreCompaniesRegistrationRequest $request,
        StorePreCompaniesRegistrationService $service
    ): JsonResponse {
        try {
            $preRegistration = $service->run($request->validated());

            return $this->successResponse(
                $preRegistration,
                'Pré-registro realizado com sucesso! Entraremos em contato em breve.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                [],
                'Não foi possível realizar o pré-registro. Tente novamente mais tarde.',
                500
            );
        }
    }
}
