<?php

namespace App\Services\Api\Athlete\BookingParticipant;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\User;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreBookingParticipantService extends BaseService
{
    public function run(array $data, Booking $booking): BookingParticipant
    {
        $userId = $this->getUserId();

        if ($booking->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Reserva não encontrada ou você não tem permissão para acessá-la.']);
        }

        if ($booking->booking_status === 'cancelado') {
            throw ValidationException::withMessages(['error' => 'Não é possível adicionar participantes a uma reserva cancelada.']);
        }

        return DB::transaction(function () use ($data, $booking, $userId): BookingParticipant {
            $data['booking_id'] = $booking->id;
            $data['added_by_user_id'] = $userId;
            $data['status'] = $data['status'] ?? 'pendente';

            if (isset($data['user_phone'])) {
                $user = User::where('phone', $data['user_phone'])->first();

                if ($user) {

                    if (BookingParticipant::query()->where('booking_id', $booking->id)->where('user_id', $user->id)->exists()) {
                        throw ValidationException::withMessages(['error' => 'Usuário já está cadastrado nesta reserva.']);
                    }

                    $data['user_id'] = $user->id;
                    $data['name'] = $user->name;
                    $data['phone'] = $user->phone;
                } else {
                    throw ValidationException::withMessages(['error' => 'Usuário com telefone informado não encontrado.']);
                }

                unset($data['user_phone']);
            }

            if (array_key_exists('amount_paid', $data) && $data['amount_paid'] === null) {
                $data['amount_paid'] = 0;
            }

            if (isset($data['amount_paid']) && $data['amount_paid'] > 0) {
                $data['is_paid'] = true;
                $data['paid_at'] = now();
            }

            return BookingParticipant::create($data)->load([
                'user:id,name,email,phone',
                'user.athleteProfile.feature:id,name',
                'user.athleteProfile.position:id,name',
                'addedByUser:id,name',
            ]);
        });
    }
}
