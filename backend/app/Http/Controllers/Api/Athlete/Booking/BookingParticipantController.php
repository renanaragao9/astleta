<?php

namespace App\Http\Controllers\Api\Athlete\Booking;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\BookingParticipant\StoreBookingParticipantRequest;
use App\Http\Requests\Api\Athlete\BookingParticipant\UpdateBookingParticipantRequest;
use App\Http\Resources\Athlete\BookingParticipantResource;
use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Services\Api\Athlete\BookingParticipant\DestroyBookingParticipantService;
use App\Services\Api\Athlete\BookingParticipant\IndexBookingParticipantService;
use App\Services\Api\Athlete\BookingParticipant\StoreBookingParticipantService;
use App\Services\Api\Athlete\BookingParticipant\UpdateBookingParticipantService;
use Illuminate\Http\JsonResponse;

class BookingParticipantController extends BaseController
{
    public function index(
        Booking $booking,
        IndexBookingParticipantService $indexBookingParticipantService
    ): JsonResponse {
        $participants = $indexBookingParticipantService->run($booking);

        return $this->successResponse(
            BookingParticipantResource::collection($participants),
            'Participantes encontrados com sucesso.'
        );
    }

    public function store(
        StoreBookingParticipantRequest $storeBookingParticipantRequest,
        Booking $booking,
        StoreBookingParticipantService $storeBookingParticipantService
    ): JsonResponse {
        $data = $storeBookingParticipantRequest->validated();
        $participant = $storeBookingParticipantService->run($data, $booking);

        return $this->successResponse(
            new BookingParticipantResource($participant),
            'Participante adicionado com sucesso.'
        );
    }

    public function update(
        UpdateBookingParticipantRequest $updateBookingParticipantRequest,
        Booking $booking,
        BookingParticipant $bookingParticipant,
        UpdateBookingParticipantService $updateBookingParticipantService
    ): JsonResponse {
        $data = $updateBookingParticipantRequest->validated();
        $updatedParticipant = $updateBookingParticipantService->run($data, $booking, $bookingParticipant);

        return $this->successResponse(
            new BookingParticipantResource($updatedParticipant),
            'Participante atualizado com sucesso.'
        );
    }

    public function destroy(
        Booking $booking,
        BookingParticipant $bookingParticipant,
        DestroyBookingParticipantService $destroyBookingParticipantService
    ): JsonResponse {
        $destroyBookingParticipantService->run($booking, $bookingParticipant);

        return $this->successResponse(
            null,
            'Participante removido com sucesso.'
        );
    }
}
