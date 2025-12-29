<?php

namespace App\Services\Api\Company\Booking;

use App\Models\Booking;
use App\Services\Api\Company\Global\BaseService;

class DeleteBookingService extends BaseService
{
    public function run(Booking $booking): void
    {
        $this->getCompany();

        if ($booking->booking_status === 'concluido') {
            throw new \Exception('Não é possível remover uma reserva já concluída.');
        }

        if ($booking->booking_status === 'confirmado') {
            $booking->update([
                'booking_status' => 'cancelado',
                'cancellation_reason' => 'Cancelado pela empresa',
            ]);
        } else {
            $booking->delete();
        }
    }
}
