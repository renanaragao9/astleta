<?php

namespace App\Http\Controllers\Api\Athlete\Team;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\TeamStatisticsBooking\StoreTeamStatisticsBookingRequest;
use App\Http\Requests\Api\Athlete\TeamStatisticsBooking\UpdateTeamStatisticsBookingRequest;
use App\Http\Resources\Athlete\TeamStatisticsBookingResource;
use App\Models\TeamBooking;
use App\Models\TeamStatisticsBooking;
use App\Services\Api\Athlete\TeamStatisticsBooking\DestroyTeamStatisticsBookingService;
use App\Services\Api\Athlete\TeamStatisticsBooking\IndexTeamStatisticsBookingService;
use App\Services\Api\Athlete\TeamStatisticsBooking\StoreTeamStatisticsBookingService;
use App\Services\Api\Athlete\TeamStatisticsBooking\UpdateTeamStatisticsBookingService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TeamStatisticsBookingController extends BaseController
{
    public function index(
        TeamBooking $teamBooking,
        IndexTeamStatisticsBookingService $indexTeamStatisticsBookingService
    ): JsonResponse {
        $statistics = $indexTeamStatisticsBookingService->run($teamBooking);

        return $this->successResponse(
            TeamStatisticsBookingResource::collection($statistics),
            'Estatísticas de time encontradas com sucesso.'
        );
    }

    public function store(
        StoreTeamStatisticsBookingRequest $storeTeamStatisticsBookingRequest,
        TeamBooking $teamBooking,
        StoreTeamStatisticsBookingService $storeTeamStatisticsBookingService
    ): JsonResponse {
        $data = $storeTeamStatisticsBookingRequest->validated();
        $statistic = $storeTeamStatisticsBookingService->run($data, $teamBooking);

        return response()->json([
            'status' => 'success',
            'message' => 'Estatística de time adicionada com sucesso.',
            'data' => new TeamStatisticsBookingResource($statistic),
        ], Response::HTTP_CREATED);
    }

    public function update(
        UpdateTeamStatisticsBookingRequest $updateTeamStatisticsBookingRequest,
        TeamBooking $teamBooking,
        TeamStatisticsBooking $teamStatisticsBooking,
        UpdateTeamStatisticsBookingService $updateTeamStatisticsBookingService
    ): JsonResponse {
        $data = $updateTeamStatisticsBookingRequest->validated();
        $updatedStatistic = $updateTeamStatisticsBookingService->run($data, $teamBooking, $teamStatisticsBooking);

        return $this->successResponse(
            new TeamStatisticsBookingResource($updatedStatistic),
            'Estatística de time atualizada com sucesso.'
        );
    }

    public function destroy(
        TeamBooking $teamBooking,
        TeamStatisticsBooking $teamStatisticsBooking,
        DestroyTeamStatisticsBookingService $destroyTeamStatisticsBookingService
    ): JsonResponse {
        $destroyTeamStatisticsBookingService->run($teamBooking, $teamStatisticsBooking);

        return $this->successResponse(
            null,
            'Estatística de time removida com sucesso.'
        );
    }
}
