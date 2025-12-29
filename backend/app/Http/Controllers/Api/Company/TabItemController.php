<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\TabItem\IndexTabItemRequest;
use App\Http\Requests\Api\Company\TabItem\StoreTabItemRequest;
use App\Http\Requests\Api\Company\TabItem\UpdateTabItemRequest;
use App\Http\Resources\Company\TabItemResource;
use App\Models\TabItem;
use App\Services\Api\Company\TabItem\DeleteTabItemService;
use App\Services\Api\Company\TabItem\IndexTabItemService;
use App\Services\Api\Company\TabItem\StoreTabItemService;
use App\Services\Api\Company\TabItem\UpdateTabItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TabItemController extends BaseController
{
    public function index(
        IndexTabItemRequest $indexTabItemRequest,
        IndexTabItemService $indexTabItemService,
    ): AnonymousResourceCollection {
        $tabItems = $indexTabItemService->run($indexTabItemRequest);

        return TabItemResource::collection($tabItems);
    }

    public function show(TabItem $tabItem): JsonResponse
    {
        return $this->successResponse(
            new TabItemResource($tabItem),
            'Item da comanda encontrado com sucesso.'
        );
    }

    public function store(
        StoreTabItemRequest $storeTabItemRequest,
        StoreTabItemService $storeTabItemService
    ): JsonResponse {
        $tabItem = $storeTabItemService->run($storeTabItemRequest->validated());

        return $this->successResponse(
            new TabItemResource($tabItem),
            'Item da comanda criado com sucesso.'
        );
    }

    public function update(
        UpdateTabItemRequest $updateTabItemRequest,
        UpdateTabItemService $updateTabItemService,
        TabItem $tabItem
    ): JsonResponse {
        $tabItem = $updateTabItemService->run($tabItem, $updateTabItemRequest->validated());

        return $this->successResponse(
            new TabItemResource($tabItem),
            'Item da comanda atualizado com sucesso.'
        );
    }

    public function destroy(TabItem $tabItem, DeleteTabItemService $deleteTabItemService): JsonResponse
    {
        $deleteTabItemService->run($tabItem);

        return $this->successResponse(
            null,
            'Item da comanda removido com sucesso.'
        );
    }
}
