<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\Supplier\IndexSupplierRequest;
use App\Http\Requests\Api\Company\Supplier\StoreSupplierRequest;
use App\Http\Requests\Api\Company\Supplier\UpdateSupplierRequest;
use App\Http\Resources\Company\SupplierResource;
use App\Models\Supplier;
use App\Services\Api\Company\Supplier\DeleteSupplierService;
use App\Services\Api\Company\Supplier\IndexSupplierService;
use App\Services\Api\Company\Supplier\SelectSupplierService;
use App\Services\Api\Company\Supplier\StoreSupplierService;
use App\Services\Api\Company\Supplier\UpdateSupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupplierController extends BaseController
{
    public function index(
        IndexSupplierRequest $indexSupplierRequest,
        IndexSupplierService $indexSupplierService,
    ): AnonymousResourceCollection {
        $data = $indexSupplierRequest->validated();
        $suppliers = $indexSupplierService->run($data);

        return SupplierResource::collection($suppliers);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return $this->successResponse(
            new SupplierResource($supplier),
            'Fornecedor encontrado com sucesso.'
        );
    }

    public function store(
        StoreSupplierRequest $storeSupplierRequest,
        StoreSupplierService $storeSupplierService
    ): JsonResponse {
        $data = $storeSupplierRequest->validated();
        $supplier = $storeSupplierService->run($data);

        return $this->successResponse(
            new SupplierResource($supplier),
            'Fornecedor criado com sucesso.'
        );
    }

    public function update(
        UpdateSupplierRequest $updateSupplierRequest,
        UpdateSupplierService $updateSupplierService,
        Supplier $supplier
    ): JsonResponse {
        $data = $updateSupplierRequest->validated();
        $supplier = $updateSupplierService->run($supplier, $data);

        return $this->successResponse(
            new SupplierResource($supplier),
            'Fornecedor atualizado com sucesso.'
        );
    }

    public function destroy(Supplier $supplier, DeleteSupplierService $deleteSupplierService): JsonResponse
    {
        $deleteSupplierService->run($supplier);

        return $this->successResponse(
            null,
            'Fornecedor removido com sucesso.'
        );
    }

    public function select(SelectSupplierService $selectSupplierService): JsonResponse
    {
        $suppliers = $selectSupplierService->run();

        return $this->successResponse(
            $suppliers,
            'Fornecedores carregados com sucesso.'
        );
    }
}