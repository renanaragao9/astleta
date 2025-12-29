<?php

namespace App\Http\Controllers\Api\Athlete\Select;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\Select\Sport\IndexSportRequest;
use App\Services\Api\Athlete\Select\Sport\IndexSportService;
use Illuminate\Http\JsonResponse;

class SportController extends BaseController
{
    public function index(
        IndexSportRequest $indexSportRequest,
        IndexSportService $indexSportService
    ): JsonResponse {
        $sports = $indexSportService->run();

        return $this->successResponse(
            $sports,
            'Esportes carregados com sucesso.'
        );
    }
}
