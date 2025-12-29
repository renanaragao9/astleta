<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\Warehouse\IndexWarehouseRequest;
use App\Http\Requests\Api\Company\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Api\Company\Warehouse\UpdateWarehouseRequest;
use App\Http\Resources\Company\WarehouseResource;
use App\Models\Warehouse;
use App\Services\Api\Company\Warehouse\DeleteWarehouseService;
use App\Services\Api\Company\Warehouse\IndexWarehouseService;
use App\Services\Api\Company\Warehouse\SelectWarehouseService;
use App\Services\Api\Company\Warehouse\StoreWarehouseService;
use App\Services\Api\Company\Warehouse\UpdateWarehouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WarehouseController extends BaseController
{
    public function index(
        IndexWarehouseRequest $indexWarehouseRequest,
        IndexWarehouseService $indexWarehouseService,
    ): AnonymousResourceCollection {
        $data = $indexWarehouseRequest->validated();
        $warehouses = $indexWarehouseService->run($data);

        return WarehouseResource::collection($warehouses);
    }

    public function show(Warehouse $warehouse): JsonResponse
    {
        return $this->successResponse(
            new WarehouseResource($warehouse),
            'Armazém encontrado com sucesso.'
        );
    }

    public function store(
        StoreWarehouseRequest $storeWarehouseRequest,
        StoreWarehouseService $storeWarehouseService
    ): JsonResponse {
        $data = $storeWarehouseRequest->validated();
        $warehouse = $storeWarehouseService->run($data);

        return $this->successResponse(
            new WarehouseResource($warehouse),
            'Armazém criado com sucesso.'
        );
    }

    public function update(
        UpdateWarehouseRequest $updateWarehouseRequest,
        UpdateWarehouseService $updateWarehouseService,
        Warehouse $warehouse
    ): JsonResponse {
        $data = $updateWarehouseRequest->validated();
        $warehouse = $updateWarehouseService->run($warehouse, $data);

        return $this->successResponse(
            new WarehouseResource($warehouse),
            'Armazém atualizado com sucesso.'
        );
    }

    public function destroy(Warehouse $warehouse, DeleteWarehouseService $deleteWarehouseService): JsonResponse
    {
        $deleteWarehouseService->run($warehouse);

        return $this->successResponse(
            null,
            'Armazém removido com sucesso.'
        );
    }

    public function select(SelectWarehouseService $selectWarehouseService): JsonResponse
    {
        $warehouses = $selectWarehouseService->run();

        return $this->successResponse(
            $warehouses,
            'Armazéns carregados com sucesso.'
        );
    }
}