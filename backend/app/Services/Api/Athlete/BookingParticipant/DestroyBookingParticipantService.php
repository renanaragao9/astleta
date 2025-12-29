<?php

namespace App\Services\Api\Athlete\BookingParticipant;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DestroyBookingParticipantService extends BaseService
{
    public function run(Booking $booking, BookingParticipant $participant): bool
    {
        $userId = $this->getUserId();

        if ($booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva não encontrada ou você não tem permissão para acessá-la.']);
        }

        if ($participant->booking_id !== $booking->id) {
            throw ValidationException::withMessages(['error' => 'Participante não encontrado.']);
        }

        if (in_array($booking->booking_status, ['cancelado', 'concluido'])) {
            throw ValidationException::withMessages(['error' => 'Não é possível remover participantes de uma reserva cancelada ou concluída.']);
        }

        return DB::transaction(callback: fn () => $participant->delete());
    }
}
