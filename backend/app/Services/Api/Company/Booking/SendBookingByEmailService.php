<?php

namespace App\Services\Api\Company\Booking;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingSent;
use App\Notifications\MessageNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class SendBookingByEmailService
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function run(int $bookingId, string $email): bool
    {
        $booking = Booking::with(['field.company', 'paymentForm'])->findOrFail($bookingId);

        if ($booking->bookingStatus === 'cancelado') {
            throw ValidationException::withMessages([
                'error' => 'Não é possível enviar reservas canceladas.',
            ]);
        }

        try {
            Notification::route('mail', $email)->notify(new BookingSent($booking));

            return true;

        } catch (\Exception $e) {
            $this->user->createNotificationMessage(get_class($this), (new MessageNotification)
                ->message('Erro ao enviar reserva por email')
                ->icon('pi-exclamation-triangle')
                ->toArray());

            throw ValidationException::withMessages([
                'error' => 'Erro ao enviar email.',
            ]);
        }
    }
}
