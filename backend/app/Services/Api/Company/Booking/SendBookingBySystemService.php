<?php

namespace App\Services\Api\Company\Booking;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingSent;
use App\Notifications\MessageNotification;
use Illuminate\Validation\ValidationException;

class SendBookingBySystemService
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function run(int $bookingId, string $phone): bool
    {
        $booking = Booking::with(['field.company', 'paymentForm'])->findOrFail($bookingId);

        if ($booking->bookingStatus === 'cancelado') {
            throw ValidationException::withMessages([
                'error' => 'Não é possível enviar reservas canceladas.',
            ]);
        }

        try {
            $userSystem = User::where('phone', $phone)->first();

            if (! $userSystem) {
                throw ValidationException::withMessages([
                    'error' => 'Usuário não encontrado com o telefone informado.',
                ]);
            }

            $booking->update(['user_id' => $userSystem->id]);

            $userSystem->notify(new BookingSent($booking));

            $userSystem->createNotificationMessage(get_class($this), (new MessageNotification)
                ->message('Olá, sua reserva foi recebida com sucesso. Verifique em "Minhas Reservas" para mais detalhes.')
                ->icon('pi-check-circle')
                ->toArray());

            return true;

        } catch (\Exception $e) {
            $this->user->createNotificationMessage(get_class($this), (new MessageNotification)
                ->message('Erro ao enviar reserva.')
                ->icon('pi-exclamation-triangle')
                ->toArray());

            throw ValidationException::withMessages([
                'error' => 'Erro ao enviar para usuário do sistema.',
            ]);
        }
    }
}
