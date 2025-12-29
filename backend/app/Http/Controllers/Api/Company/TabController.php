<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\Tab\IndexTabRequest;
use App\Http\Requests\Api\Company\Tab\SendTabRequest;
use App\Http\Requests\Api\Company\Tab\StoreTabRequest;
use App\Http\Requests\Api\Company\Tab\UpdateTabRequest;
use App\Http\Resources\Company\TabResource;
use App\Models\Tab;
use App\Services\Api\Company\Tab\DeleteTabService;
use App\Services\Api\Company\Tab\IndexTabService;
use App\Services\Api\Company\Tab\SendTabByEmailService;
use App\Services\Api\Company\Tab\SendTabBySystemService;
use App\Services\Api\Company\Tab\StoreTabService;
use App\Services\Api\Company\Tab\UpdateTabService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TabController extends BaseController
{
    public function index(
        IndexTabRequest $indexTabRequest,
        IndexTabService $indexTabService,
    ): AnonymousResourceCollection {
        $data = $indexTabRequest->validated();
        $tabs = $indexTabService->run($data);

        return TabResource::collection($tabs);
    }

    public function show(Tab $tab): JsonResponse
    {
        return $this->successResponse(
            new TabResource($tab),
            'Comanda encontrada com sucesso.'
        );
    }

    public function store(
        StoreTabRequest $storeTabRequest,
        StoreTabService $storeTabService
    ): JsonResponse {
        $data = $storeTabRequest->validated();
        $tab = $storeTabService->run($data);

        return $this->successResponse(
            new TabResource($tab),
            'Comanda criada com sucesso.'
        );
    }

    public function update(
        UpdateTabRequest $updateTabRequest,
        UpdateTabService $updateTabService,
        Tab $tab
    ): JsonResponse {
        $data = $updateTabRequest->validated();
        $tab = $updateTabService->run($tab, $data);

        return $this->successResponse(
            new TabResource($tab),
            'Comanda atualizada com sucesso.'
        );
    }

    public function destroy(Tab $tab, DeleteTabService $deleteTabService): JsonResponse
    {
        $deleteTabService->run($tab);

        return $this->successResponse(
            null,
            'Comanda removida com sucesso.'
        );
    }

    public function send(
        SendTabRequest $sendTabRequest,
        SendTabByEmailService $sendTabByEmailService,
        SendTabBySystemService $sendTabBySystemService
    ): JsonResponse {
        $data = $sendTabRequest->validated();

        if ($data['send_method'] === 'email') {
            $sendTabByEmailService->run($data['tab_id'], $data['email']);
            $message = 'Comanda enviada com sucesso por email.';
        } else {
            $sendTabBySystemService->run($data['tab_id'], $data['phone']);
            $message = 'Comanda enviada com sucesso para o usuário do sistema.';
        }

        return $this->successResponse(
            ['success' => true],
            $message
        );
    }
}
