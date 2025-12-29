<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Public\Field\IndexPublicFieldRequest;
use App\Http\Resources\Public\PublicCompanyResource;
use App\Http\Resources\Public\PublicFieldResource;
use App\Http\Resources\Public\PublicFieldViewResource;
use App\Models\Company;
use App\Models\Field;
use App\Services\Api\Public\Company\IndexPublicCompanyService;
use App\Services\Api\Public\Field\IndexPublicFieldService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicFieldController extends BaseController
{
    public function index(
        IndexPublicFieldRequest $indexPublicFieldRequest,
        IndexPublicFieldService $indexPublicFieldService,
    ): AnonymousResourceCollection {
        $data = $indexPublicFieldRequest->validated();
        $fields = $indexPublicFieldService->run($data);

        return PublicFieldResource::collection($fields);
    }

    public function show(Field $field): JsonResponse
    {
        $field->load([
            'fieldType',
            'fieldSurface',
            'fieldSize',
            'fieldItems',
            'fieldSchedules',
            'company',
            'company.addresses',
            'fieldImages',
        ]);

        if (! $field->is_active || $field->company->status !== 'aprovado') {
            return $this->errorResponse(
                ['field' => 'Campo não disponível.'],
                'Campo não disponível.',
                404
            );
        }

        return $this->successResponse(
            new PublicFieldViewResource($field),
            'Campo encontrado com sucesso.'
        );
    }

    public function showCompanyProfile(
        Company $company,
        IndexPublicCompanyService $indexPublicCompanyService,
    ): JsonResponse {

        $result = $indexPublicCompanyService->run($company);

        return $this->successResponse(
            [
                'company' => new PublicCompanyResource($result['company']),
                'fields' => PublicFieldResource::collection($result['fields']),
            ],
            'Perfil da empresa encontrado com sucesso.'
        );
    }
}
