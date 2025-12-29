<?php

namespace App\Http\Controllers\Api\Athlete\Select;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\Select\Statistic\IndexStatisticRequest;
use App\Services\Api\Athlete\Select\Statistic\GetStatisticBySportService;
use App\Services\Api\Athlete\Select\Statistic\IndexStatisticService;
use Illuminate\Http\JsonResponse;

class StatisticsController extends BaseController
{
    public function index(
        IndexStatisticRequest $indexStatisticRequest,
        IndexStatisticService $indexStatisticService
    ): JsonResponse {
        $statistics = $indexStatisticService->run();

        return $this->successResponse(
            $statistics,
            'Estatísticas encontradas com sucesso.'
        );
    }

    public function getBySport(
        int $sportId,
        GetStatisticBySportService $getStatisticBySportService
    ): JsonResponse {
        $statistics = $getStatisticBySportService->run($sportId);

        return $this->successResponse(
            $statistics,
            'Estatísticas encontradas com sucesso.'
        );
    }
}
