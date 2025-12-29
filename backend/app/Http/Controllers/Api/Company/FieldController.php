<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\Field\IndexFieldRequest;
use App\Http\Requests\Api\Company\Field\StoreFieldRequest;
use App\Http\Requests\Api\Company\Field\UpdateFieldImageRequest;
use App\Http\Requests\Api\Company\Field\UpdateFieldRequest;
use App\Http\Resources\Company\FieldResource;
use App\Models\Field;
use App\Services\Api\Company\Field\DeleteFieldService;
use App\Services\Api\Company\Field\IndexFieldService;
use App\Services\Api\Company\Field\StoreFieldService;
use App\Services\Api\Company\Field\UpdateFieldImageService;
use App\Services\Api\Company\Field\UpdateFieldService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FieldController extends BaseController
{
    public function index(
        IndexFieldRequest $indexFieldRequest,
        IndexFieldService $indexFieldService,
    ): AnonymousResourceCollection {
        $data = $indexFieldRequest->validated();
        $fields = $indexFieldService->run($data);

        return FieldResource::collection($fields);
    }

    public function show(Field $field): JsonResponse
    {
        return $this->successResponse(
            new FieldResource($field),
            'Arena encontrada com sucesso.'
        );
    }

    public function store(
        StoreFieldRequest $storeFieldRequest,
        StoreFieldService $storeFieldService
    ): JsonResponse {
        $data = $storeFieldRequest->validated();
        $field = $storeFieldService->run($data);

        return $this->successResponse(
            new FieldResource($field),
            'Arena criada com sucesso.'
        );
    }

    public function update(
        UpdateFieldRequest $updateFieldRequest,
        UpdateFieldService $updateFieldService,
        Field $field
    ): JsonResponse {
        $data = $updateFieldRequest->validated();
        $field = $updateFieldService->run($field, $data);

        return $this->successResponse(
            new FieldResource($field),
            'Arena atualizada com sucesso.'
        );
    }

    public function destroy(Field $field, DeleteFieldService $deleteFieldService): JsonResponse
    {
        $deleteFieldService->run($field);

        return $this->successResponse(
            null,
            'Arena removida com sucesso.'
        );
    }

    public function updateImage(
        UpdateFieldImageRequest $updateFieldImageRequest,
        UpdateFieldImageService $updateFieldImageService,
        Field $field
    ): JsonResponse {
        $result = $updateFieldImageService->run(
            $field,
            $updateFieldImageRequest->file('image')
        );

        return $this->successResponse(
            $result['data'],
            $result['message']
        );
    }
}
