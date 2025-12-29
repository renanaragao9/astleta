<?php

namespace App\Services\Api\Athlete\Racha;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Validation\ValidationException;

class JoinAthleteRachaService extends BaseService
{
    public function run(array $data): BookingParticipant
    {
        $user = $this->getUser();
        $rachaNumber = $data['racha_number'];

        $booking = Booking::where('booking_number', $rachaNumber)->first();

        $existingParticipant = BookingParticipant::where('booking_id', $booking->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $booking) {
            throw ValidationException::withMessages(['error' => 'Racha não encontrado.']);
        }

        if ($existingParticipant) {
            throw ValidationException::withMessages(['error' => 'Você já está participando deste racha.']);
        }

        if (in_array($booking->booking_status, ['cancelado', 'concluido', 'pendente'])) {
            throw ValidationException::withMessages(['error' => 'Este racha não está disponível para participação no momento.']);
        }

        return BookingParticipant::create([
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'added_by_user_id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'confirmed_at' => now(),
            'status' => 'confirmado',
        ]);
    }
}
