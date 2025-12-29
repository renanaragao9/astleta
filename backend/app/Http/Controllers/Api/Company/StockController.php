<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\Stock\IndexStockRequest;
use App\Http\Requests\Api\Company\Stock\StoreStockRequest;
use App\Http\Requests\Api\Company\Stock\UpdateStockRequest;
use App\Http\Resources\Company\StockResource;
use App\Models\Stock;
use App\Services\Api\Company\Stock\DeleteStockService;
use App\Services\Api\Company\Stock\IndexStockService;
use App\Services\Api\Company\Stock\StoreStockService;
use App\Services\Api\Company\Stock\UpdateStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockController extends BaseController
{
    public function index(
        IndexStockRequest $indexStockRequest,
        IndexStockService $indexStockService,
    ): AnonymousResourceCollection {
        $data = $indexStockRequest->validated();
        $stocks = $indexStockService->run($data);

        return StockResource::collection($stocks);
    }

    public function show(Stock $stock): JsonResponse
    {
        return $this->successResponse(
            new StockResource($stock),
            'Estoque encontrado com sucesso.'
        );
    }

    public function store(
        StoreStockRequest $storeStockRequest,
        StoreStockService $storeStockService
    ): JsonResponse {
        $data = $storeStockRequest->validated();
        $stock = $storeStockService->run($data);

        return $this->successResponse(
            new StockResource($stock),
            'Estoque criado com sucesso.'
        );
    }

    public function update(
        UpdateStockRequest $updateStockRequest,
        UpdateStockService $updateStockService,
        Stock $stock
    ): JsonResponse {
        $data = $updateStockRequest->validated();
        $stock = $updateStockService->run($stock, $data);

        return $this->successResponse(
            new StockResource($stock),
            'Estoque atualizado com sucesso.'
        );
    }

    public function destroy(Stock $stock, DeleteStockService $deleteStockService): JsonResponse
    {
        $deleteStockService->run($stock);

        return $this->successResponse(
            null,
            'Estoque removido com sucesso.'
        );
    }
}