<?php

namespace App\Observers;

use App\Models\Booking;
use App\Notifications\BookingStatusNotification;
use App\Notifications\MessageNotification;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        $this->sendNotificationIfNeeded($booking, 'created');

        $owner = $booking->field->company->user;
        $message = 'Uma nova reserva '.$booking->booking_number.' foi criada para o seu campo '.$booking->field->name.'. Verifique em "Reservas" para mais detalhes.';

        $owner->createNotificationMessage(
            get_class($booking),
            (new MessageNotification)
                ->message($message)
                ->icon('pi-plus-circle')
                ->toArray()
        );

        $owner->notify(new BookingStatusNotification($booking, 'created', true));
    }

    public function updated(Booking $booking): void
    {
        if ($booking->wasChanged('booking_status')) {
            $this->sendNotificationIfNeeded($booking, 'updated');
        }
    }

    private function sendNotificationIfNeeded(Booking $booking, string $type): void
    {
        if ($booking->user_id !== $booking->field->company->user_id) {
            $message = $type === 'created'
                ? 'Sua reserva '.$booking->booking_number.' foi recebida com sucesso. Verifique em "Minhas Reservas" para mais detalhes.'
                : 'Sua reserva '.$booking->booking_number.' foi atualizada para o status: '.ucfirst($booking->booking_status).'. Verifique em "Minhas Reservas" para mais detalhes.';

            $booking->user->createNotificationMessage(
                get_class($booking),
                (new MessageNotification)
                    ->message($message)
                    ->icon('pi-check-circle')
                    ->toArray()
            );
        }
    }

    public function deleted(Booking $booking): void
    {
        //
    }

    public function restored(Booking $booking): void
    {
        //
    }

    public function forceDeleted(Booking $booking): void
    {
        //
    }
}
