<?php

namespace App\Services\Api\Company\Booking;

use App\Models\Booking;
use App\Models\CompanyTransfer;
use App\Services\Api\Company\Global\BaseService;
use Illuminate\Validation\ValidationException;

class UpdateStatusBookingService extends BaseService
{
    public function run(Booking $booking, array $data): Booking
    {
        $updateData = [
            'booking_status' => $data['bookingStatus'],
        ];

        if ($booking->booking_status == 'concluido' || $booking->booking_status == 'cancelado') {
            throw ValidationException::withMessages([
                'error' => 'Reserva não pode ser alterada.',
            ]);
        }

        if (isset($data['cancellation_reason'])) {
            $updateData['cancellation_reason'] = $data['cancellation_reason'];
        }

        $booking->update($updateData);

        if ($data['bookingStatus'] === 'cancelado') {
            CompanyTransfer::where('booking_id', $booking->id)->delete();
        }

        return $booking->load(['field', 'user', 'coupon', 'paymentForm']);
    }
}
