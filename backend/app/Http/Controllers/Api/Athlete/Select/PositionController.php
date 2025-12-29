<?php

namespace App\Http\Controllers\Api\Athlete\Select;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\Select\Position\IndexPositionRequest;
use App\Services\Api\Athlete\Select\Position\IndexPositionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PositionController extends BaseController
{
    public function index(
        Request $request,
        IndexPositionRequest $indexPositionRequest,
        IndexPositionService $indexPositionService
    ): JsonResponse {
        $positions = $indexPositionService->run($request);

        return $this->successResponse(
            $positions,
            'Posições carregadas com sucesso.'
        );
    }
}
