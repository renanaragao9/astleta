<?php

namespace App\Http\Controllers\Api\Athlete\Booking;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\BookingRating\StoreBookingRatingRequest;
use App\Http\Requests\Api\Athlete\BookingRating\UpdateBookingRatingRequest;
use App\Http\Resources\Athlete\BookingRatingResource;
use App\Models\Booking;
use App\Models\PlayerRating;
use App\Services\Api\Athlete\BookingRating\DestroyBookingRatingService;
use App\Services\Api\Athlete\BookingRating\IndexBookingRatingService;
use App\Services\Api\Athlete\BookingRating\StoreBookingRatingService;
use App\Services\Api\Athlete\BookingRating\UpdateBookingRatingService;
use Illuminate\Http\JsonResponse;

class BookingRatingController extends BaseController
{
    public function index(
        Booking $booking,
        IndexBookingRatingService $indexBookingRatingService
    ): JsonResponse {
        $ratings = $indexBookingRatingService->run($booking);

        return $this->successResponse(
            BookingRatingResource::collection($ratings),
            'Avaliações encontradas com sucesso.'
        );
    }

    public function store(
        StoreBookingRatingRequest $storeBookingRatingRequest,
        Booking $booking,
        StoreBookingRatingService $storeBookingRatingService
    ): JsonResponse {
        $data = $storeBookingRatingRequest->validated();
        $rating = $storeBookingRatingService->run($data, $booking);

        return $this->successResponse(
            new BookingRatingResource($rating),
            'Avaliação adicionada com sucesso.'
        );
    }

    public function update(
        UpdateBookingRatingRequest $updateBookingRatingRequest,
        Booking $booking,
        PlayerRating $playerRating,
        UpdateBookingRatingService $updateBookingRatingService
    ): JsonResponse {
        $data = $updateBookingRatingRequest->validated();
        $updatedRating = $updateBookingRatingService->run($data, $booking, $playerRating);

        return $this->successResponse(
            new BookingRatingResource($updatedRating),
            'Avaliação atualizada com sucesso.'
        );
    }

    public function destroy(
        Booking $booking,
        PlayerRating $playerRating,
        DestroyBookingRatingService $destroyBookingRatingService
    ): JsonResponse {
        $destroyBookingRatingService->run($booking, $playerRating);

        return $this->successResponse(
            null,
            'Avaliação removida com sucesso.'
        );
    }
}
