<?php

namespace App\Http\Controllers\Api\Athlete\Booking;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\BookingStatistic\StoreBookingStatisticRequest;
use App\Http\Requests\Api\Athlete\BookingStatistic\UpdateBookingStatisticRequest;
use App\Http\Resources\Athlete\BookingStatisticResource;
use App\Models\Booking;
use App\Models\PlayerStatistic;
use App\Services\Api\Athlete\BookingStatistic\DestroyBookingStatisticService;
use App\Services\Api\Athlete\BookingStatistic\IndexBookingStatisticService;
use App\Services\Api\Athlete\BookingStatistic\StoreBookingStatisticService;
use App\Services\Api\Athlete\BookingStatistic\UpdateBookingStatisticService;
use Illuminate\Http\JsonResponse;

class BookingStatisticController extends BaseController
{
    public function index(
        Booking $booking,
        IndexBookingStatisticService $indexBookingStatisticService
    ): JsonResponse {
        $statistics = $indexBookingStatisticService->run($booking);

        return $this->successResponse(
            BookingStatisticResource::collection($statistics),
            'Estatísticas encontradas com sucesso.'
        );
    }

    public function store(
        StoreBookingStatisticRequest $storeBookingStatisticRequest,
        Booking $booking,
        StoreBookingStatisticService $storeBookingStatisticService
    ): JsonResponse {
        $data = $storeBookingStatisticRequest->validated();
        $statistic = $storeBookingStatisticService->run($data, $booking);

        return $this->successResponse(
            new BookingStatisticResource($statistic),
            'Estatística adicionada com sucesso.'
        );
    }

    public function update(
        UpdateBookingStatisticRequest $updateBookingStatisticRequest,
        Booking $booking,
        PlayerStatistic $playerStatistic,
        UpdateBookingStatisticService $updateBookingStatisticService
    ): JsonResponse {
        $data = $updateBookingStatisticRequest->validated();
        $updatedStatistic = $updateBookingStatisticService->run($data, $booking, $playerStatistic);

        return $this->successResponse(
            new BookingStatisticResource($updatedStatistic),
            'Estatística atualizada com sucesso.'
        );
    }

    public function destroy(
        Booking $booking,
        PlayerStatistic $playerStatistic,
        DestroyBookingStatisticService $destroyBookingStatisticService
    ): JsonResponse {
        $destroyBookingStatisticService->run($booking, $playerStatistic);

        return $this->successResponse(
            null,
            'Estatística removida com sucesso.'
        );
    }
}
