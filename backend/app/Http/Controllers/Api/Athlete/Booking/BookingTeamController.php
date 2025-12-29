<?php

namespace App\Http\Controllers\Api\Athlete\Booking;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\TeamBooking\StoreTeamBookingRequest;
use App\Http\Requests\Api\Athlete\TeamBooking\UpdateTeamBookingRequest;
use App\Http\Resources\Athlete\TeamBookingResource;
use App\Models\Booking;
use App\Services\Api\Athlete\TeamBooking\DestroyTeamBookingService;
use App\Services\Api\Athlete\TeamBooking\IndexTeamBookingService;
use App\Services\Api\Athlete\TeamBooking\StoreTeamBookingService;
use App\Services\Api\Athlete\TeamBooking\UpdateTeamBookingService;
use Illuminate\Http\JsonResponse;

class BookingTeamController extends BaseController
{
    public function show(
        Booking $booking,
        IndexTeamBookingService $indexTeamBookingService
    ): JsonResponse {
        $teamBooking = $indexTeamBookingService->run($booking);

        if (!$teamBooking) {
            return $this->successResponse(
                null,
                'Times do jogo não encontrados.'
            );
        }

        return $this->successResponse(
            new TeamBookingResource($teamBooking),
            'Times do jogo obtidos com sucesso.'
        );
    }

    public function store(
        StoreTeamBookingRequest $storeTeamBookingRequest,
        Booking $booking,
        StoreTeamBookingService $storeTeamBookingService
    ): JsonResponse {
        $data = $storeTeamBookingRequest->validated();
        $result = $storeTeamBookingService->run($data, $booking);

        return $this->successResponse(
            new TeamBookingResource($result),
            'Times do jogo adicionados com sucesso.'
        );
    }

    public function update(
        UpdateTeamBookingRequest $updateTeamBookingRequest,
        Booking $booking,
        UpdateTeamBookingService $updateTeamBookingService
    ): JsonResponse {
        $data = $updateTeamBookingRequest->validated();
        $teamBooking = $updateTeamBookingService->run($data, $booking);

        return $this->successResponse(
            new TeamBookingResource($teamBooking),
            'Times do jogo atualizados com sucesso.'
        );
    }

    public function destroy(
        Booking $booking,
        DestroyTeamBookingService $destroyTeamBookingService
    ): JsonResponse {
        $destroyTeamBookingService->run($booking);

        return $this->successResponse(
            null,
            'Times do jogo removidos com sucesso.'
        );
    }
}
