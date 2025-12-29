<?php

namespace App\Http\Controllers\Api\Athlete\Booking;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\Booking\IndexBookingAthleteRequest;
use App\Http\Resources\Athlete\BookingAthleteResource;
use App\Models\Booking;
use App\Services\Api\Athlete\Booking\IndexBookingAthleteService;
use App\Services\Api\Athlete\Booking\ViewBookingAthleteService;
use App\Services\Pdf\BookingPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class BookingAthleteController extends BaseController
{
    public function index(
        IndexBookingAthleteRequest $indexBookingAthleteRequest,
        IndexBookingAthleteService $indexBookingAthleteService
    ): AnonymousResourceCollection {
        $data = $indexBookingAthleteRequest->validated();
        $bookings = $indexBookingAthleteService->run($data);

        return BookingAthleteResource::collection($bookings);
    }

    public function show(
        Booking $booking,
        ViewBookingAthleteService $viewBookingAthleteService
    ): JsonResponse {
        $validatedBooking = $viewBookingAthleteService->run($booking);

        return $this->successResponse(
            new BookingAthleteResource($validatedBooking),
            'Reserva encontrada com sucesso.'
        );
    }

    public function downloadReceipt(
        Booking $booking,
        ViewBookingAthleteService $viewBookingAthleteService,
        BookingPdfService $bookingPdfService
    ): Response {
        $validatedBooking = $viewBookingAthleteService->run($booking);
        $pdfContent = $bookingPdfService->generateBookingReceiptPdfContent($validatedBooking);
        $filename = strtoupper("recibo_reserva_{$validatedBooking->id}.pdf");

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }
}
