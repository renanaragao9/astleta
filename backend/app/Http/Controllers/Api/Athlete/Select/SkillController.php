<?php

namespace App\Http\Controllers\Api\Athlete\Select;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\Select\Skill\IndexSkillRequest;
use App\Services\Api\Athlete\Select\Skill\IndexSkillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillController extends BaseController
{
    public function index(
        Request $request,
        IndexSkillRequest $indexSkillRequest,
        IndexSkillService $indexSkillService
    ): JsonResponse {
        $skills = $indexSkillService->run($request);

        return $this->successResponse(
            $skills,
            'Habilidades carregadas com sucesso.'
        );
    }
}
