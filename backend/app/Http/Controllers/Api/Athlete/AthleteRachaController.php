<?php

namespace App\Http\Controllers\Api\Athlete;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\Racha\IndexAthleteRachaRequest;
use App\Http\Requests\Api\Athlete\Racha\JoinAthleteRachaRequest;
use App\Http\Resources\Athlete\RachaResource;
use App\Services\Api\Athlete\Racha\IndexAthleteRachaService;
use App\Services\Api\Athlete\Racha\JoinAthleteRachaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AthleteRachaController extends BaseController
{
    public function index(
        IndexAthleteRachaRequest $indexAthleteRachaRequest,
        IndexAthleteRachaService $indexAthleteRachaService
    ): AnonymousResourceCollection {
        $data = $indexAthleteRachaRequest->validated();
        $rachas = $indexAthleteRachaService->run($data);

        return RachaResource::collection($rachas);
    }

    public function join(
        JoinAthleteRachaRequest $joinAthleteRachaRequest,
        JoinAthleteRachaService $joinAthleteRachaService
    ): JsonResponse {
        $joined = $joinAthleteRachaService->run($joinAthleteRachaRequest->validated());

        return $this->successResponse($joined, 'Participação no racha realizada com sucesso.');
    }
}
