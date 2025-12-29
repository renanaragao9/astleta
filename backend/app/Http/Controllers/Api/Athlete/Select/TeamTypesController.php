<?php

namespace App\Http\Controllers\Api\Athlete\Select;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\Select\TeamType\IndexTeamTypeRequest;
use App\Services\Api\Athlete\Select\TeamType\IndexTeamTypeService;
use Illuminate\Http\JsonResponse;

class TeamTypesController extends BaseController
{
    public function index(
        IndexTeamTypeRequest $indexTeamTypeRequest,
        IndexTeamTypeService $indexTeamTypeService
    ): JsonResponse {
        $teamTypes = $indexTeamTypeService->run();

        return $this->successResponse(
            $teamTypes,
            'Tipos de equipe carregados com sucesso.'
        );
    }
}
