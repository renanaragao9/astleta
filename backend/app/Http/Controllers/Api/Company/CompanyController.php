<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\Company\UpdateCompanyImageRequest;
use App\Http\Resources\Company\CompanyViewResource;
use App\Services\Api\Company\Company\UpdateCompanyImageService;
use App\Services\Api\Company\Company\ViewCompanyService;
use Illuminate\Http\JsonResponse;


class CompanyController extends BaseController
{
    public function show(
        ViewCompanyService $viewCompanyService
    ): JsonResponse {
        $company = $viewCompanyService->run();

        return $this->successResponse(
            new CompanyViewResource($company),
            'Empresa encontrada com sucesso.'
        );

    }

    public function updateImage(
        UpdateCompanyImageRequest $updateCompanyImageRequest,
        UpdateCompanyImageService $updateCompanyImageService
    ): JsonResponse {
        $result = $updateCompanyImageService->run(
            $updateCompanyImageRequest->file('image')
        );

        return $this->successResponse(
            $result['data'],
            $result['message']
        );
    }
}
