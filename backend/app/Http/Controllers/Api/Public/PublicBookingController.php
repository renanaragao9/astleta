<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Public\Booking\AvailabilityPublicBookingRequest;
use App\Http\Requests\Api\Public\Booking\CalculatePricePublicBookingRequest;
use App\Http\Requests\Api\Public\Booking\StorePublicBookingRequest;
use App\Http\Resources\Public\AvailabilityPublicResource;
use App\Http\Resources\Public\CalculatePricePublicResource;
use App\Services\Api\Public\Booking\AvailabilityPublicBookingService;
use App\Services\Api\Public\Booking\CalculatePricePublicBookingService;
use App\Services\Api\Public\Booking\StorePublicBookingService;
use Illuminate\Http\JsonResponse;

class PublicBookingController extends BaseController
{
    public function __construct()
    {
        $this->middleware('throttle:5,1')->only('store');
    }

    public function store(
        StorePublicBookingRequest $storePublicBookingRequest,
        StorePublicBookingService $storePublicBookingService
    ): JsonResponse {
        $data = $storePublicBookingRequest->validated();
        $storePublicBookingService->run($data);

        return $this->successResponse(
            [],
            'Reserva criada com sucesso.'
        );
    }

    public function availability(
        AvailabilityPublicBookingRequest $availabilityPublicBookingRequest,
        AvailabilityPublicBookingService $availabilityPublicBookingService
    ): JsonResponse {
        $data = $availabilityPublicBookingRequest->validated();
        $data = $availabilityPublicBookingService->run($data);

        return $this->successResponse(
            new AvailabilityPublicResource($data),
            'Disponibilidade consultada com sucesso.'
        );
    }

    public function calculatePrice(
        CalculatePricePublicBookingRequest $calculatePricePublicBookingRequest,
        CalculatePricePublicBookingService $calculatePricePublicBookingService
    ): JsonResponse {
        $data = $calculatePricePublicBookingRequest->validated();
        $calculatePrice = $calculatePricePublicBookingService->run($data);

        return $this->successResponse(
            new CalculatePricePublicResource($calculatePrice),
            'Preço calculado com sucesso.'
        );
    }
}
