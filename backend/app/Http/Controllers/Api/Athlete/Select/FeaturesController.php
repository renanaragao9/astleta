<?php

namespace App\Http\Controllers\Api\Athlete\Select;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\Select\Feature\IndexFeatureRequest;
use App\Services\Api\Athlete\Select\Feature\IndexFeatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeaturesController extends BaseController
{
    public function index(
        Request $request,
        IndexFeatureRequest $indexFeatureRequest,
        IndexFeatureService $indexFeatureService
    ): JsonResponse {
        $features = $indexFeatureService->run($request);

        return $this->successResponse(
            $features,
            'Características carregadas com sucesso.'
        );
    }
}
