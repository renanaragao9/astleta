<?php

namespace App\Http\Controllers\Api\Athlete\Select;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\Select\TeamSelect\IndexTeamSelectRequest;
use App\Services\Api\Athlete\Select\TeamSelect\IndexTeamSelectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamSelectController extends BaseController
{
    /**
     * Get available teams for a specific sport
     */
    public function index(
        Request $request,
        IndexTeamSelectRequest $indexTeamSelectRequest,
        IndexTeamSelectService $indexTeamSelectService
    ): JsonResponse {
        $teams = $indexTeamSelectService->run($request);

        return $this->successResponse(
            $teams,
            'Times obtidos com sucesso'
        );
    }
}
