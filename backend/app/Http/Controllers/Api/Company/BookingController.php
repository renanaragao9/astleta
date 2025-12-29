<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\Booking\AvailabilityBookingRequest;
use App\Http\Requests\Api\Company\Booking\CalculatePriceBookingRequest;
use App\Http\Requests\Api\Company\Booking\GetByMonthBookingRequest;
use App\Http\Requests\Api\Company\Booking\IndexBookingRequest;
use App\Http\Requests\Api\Company\Booking\SendBookingRequest;
use App\Http\Requests\Api\Company\Booking\StoreBookingRequest;
use App\Http\Requests\Api\Company\Booking\UpdateStatusBookingRequest;
use App\Http\Resources\Company\AvailabilityResource;
use App\Http\Resources\Company\BookingResource;
use App\Http\Resources\Company\CalculatePriceResource;
use App\Http\Resources\Company\SimpleBookingResource;
use App\Models\Booking;
use App\Services\Api\Company\Booking\AvailabilityBookingService;
use App\Services\Api\Company\Booking\CalculatePriceBookingService;
use App\Services\Api\Company\Booking\GetByMonthBookingService;
use App\Services\Api\Company\Booking\IndexBookingService;
use App\Services\Api\Company\Booking\SendBookingByEmailService;
use App\Services\Api\Company\Booking\SendBookingBySystemService;
use App\Services\Api\Company\Booking\StoreBookingService;
use App\Services\Api\Company\Booking\UpdateStatusBookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookingController extends BaseController
{
    public function index(
        IndexBookingRequest $indexBookingRequest,
        IndexBookingService $indexBookingService,
    ): AnonymousResourceCollection {
        $data = $indexBookingRequest->validated();
        $bookings = $indexBookingService->run($data);

        return BookingResource::collection($bookings);
    }

    public function store(
        StoreBookingRequest $storeBookingRequest,
        StoreBookingService $storeBookingService
    ): JsonResponse {
        $data = $storeBookingRequest->validated();
        $booking = $storeBookingService->run($data);

        return $this->successResponse(
            new BookingResource($booking),
            'Reserva criada com sucesso.'
        );
    }

    public function send(
        SendBookingRequest $sendBookingRequest,
        SendBookingByEmailService $sendBookingByEmailService,
        SendBookingBySystemService $sendBookingBySystemService
    ): JsonResponse {
        $data = $sendBookingRequest->validated();

        if ($data['send_method'] === 'email') {
            $sendBookingByEmailService->run($data['booking_id'], $data['email']);
        } else {
            $sendBookingBySystemService->run($data['booking_id'], $data['phone']);
        }

        $message = $data['send_method'] === 'email'
            ? 'Reserva enviada com sucesso por email.'
            : 'Reserva enviada com sucesso para o usuário do sistema.';

        return $this->successResponse(
            ['success' => true],
            $message
        );
    }

    public function updateStatus(
        UpdateStatusBookingRequest $updateStatusBookingRequest,
        Booking $booking,
        UpdateStatusBookingService $updateStatusService
    ): JsonResponse {
        $data = $updateStatusBookingRequest->validated();
        $booking = $updateStatusService->run($booking, $data);

        return $this->successResponse(
            new BookingResource($booking),
            'Status da reserva atualizado com sucesso.'
        );
    }

    public function availability(
        AvailabilityBookingRequest $availabilityBookingRequest,
        AvailabilityBookingService $availabilityBookingService
    ): JsonResponse {
        $data = $availabilityBookingRequest->validated();
        $availability = $availabilityBookingService->run($data);

        return $this->successResponse(
            new AvailabilityResource($availability),
            'Disponibilidade consultada com sucesso.'
        );
    }

    public function calculatePrice(
        CalculatePriceBookingRequest $calculatePriceBookingRequest,
        CalculatePriceBookingService $calculatePriceBookingService
    ): JsonResponse {
        $data = $calculatePriceBookingRequest->validated();
        $data = $calculatePriceBookingService->run($data);

        return $this->successResponse(
            new CalculatePriceResource($data),
            'Preço calculado com sucesso.'
        );
    }

    public function getByMonth(
        GetByMonthBookingRequest $getByMonthBookingRequest,
        GetByMonthBookingService $getByMonthBookingService
    ): AnonymousResourceCollection {
        $data = $getByMonthBookingRequest->validated();
        $bookings = $getByMonthBookingService->run($data);

        return SimpleBookingResource::collection($bookings);
    }
}
