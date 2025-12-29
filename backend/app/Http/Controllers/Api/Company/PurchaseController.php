<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\Purchase\IndexPurchaseRequest;
use App\Http\Requests\Api\Company\Purchase\StorePurchaseRequest;
use App\Http\Requests\Api\Company\Purchase\UpdatePurchaseRequest;
use App\Http\Resources\Company\PurchaseResource;
use App\Models\Purchase;
use App\Services\Api\Company\Purchase\DeletePurchaseService;
use App\Services\Api\Company\Purchase\IndexPurchaseService;
use App\Services\Api\Company\Purchase\StorePurchaseService;
use App\Services\Api\Company\Purchase\UpdatePurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PurchaseController extends BaseController
{
    public function index(
        IndexPurchaseRequest $indexPurchaseRequest,
        IndexPurchaseService $indexPurchaseService,
    ): AnonymousResourceCollection {
        $data = $indexPurchaseRequest->validated();
        $purchases = $indexPurchaseService->run($data);

        return PurchaseResource::collection($purchases);
    }

    public function show(Purchase $purchase): JsonResponse
    {
        return $this->successResponse(
            new PurchaseResource($purchase),
            'Compra encontrada com sucesso.'
        );
    }

    public function store(
        StorePurchaseRequest $storePurchaseRequest,
        StorePurchaseService $storePurchaseService
    ): JsonResponse {
        $data = $storePurchaseRequest->validated();
        $purchase = $storePurchaseService->run($data);

        return $this->successResponse(
            new PurchaseResource($purchase),
            'Compra criada com sucesso.'
        );
    }

    public function update(
        Purchase $purchase,
        UpdatePurchaseRequest $updatePurchaseRequest,
        UpdatePurchaseService $updatePurchaseService
    ): JsonResponse {
        $data = $updatePurchaseRequest->validated();
        $updatedPurchase = $updatePurchaseService->run($purchase, $data);

        return $this->successResponse(
            new PurchaseResource($updatedPurchase),
            'Compra atualizada com sucesso.'
        );
    }

    public function destroy(Purchase $purchase, DeletePurchaseService $deletePurchaseService): JsonResponse
    {
        $deletePurchaseService->run($purchase);

        return $this->successResponse(
            null,
            'Compra cancelada com sucesso.'
        );
    }
}