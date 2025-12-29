<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\Financial\IndexFinancialRequest;
use App\Http\Resources\Company\FinancialResource;
use App\Services\Api\Company\Financial\IndexFinancialService;
use Illuminate\Http\JsonResponse;

class FinancialController extends BaseController
{
    public function index(
        IndexFinancialRequest $indexFinancialRequest,
        IndexFinancialService $indexFinancialService
    ): JsonResponse {
        $data = $indexFinancialService->run($indexFinancialRequest);

        return $this->successResponse(
            new FinancialResource($data),
            'Saldo do período calculado com sucesso.'
        );

    }
}
