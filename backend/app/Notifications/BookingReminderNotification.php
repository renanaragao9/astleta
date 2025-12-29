<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking;

        return (new MailMessage)
            ->subject("Lembrete: Reserva {$booking->booking_number} - {$booking->field->name}")
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line("Falta 30 minutos para começar sua reserva {$booking->booking_number} no campo {$booking->field->name}.")
            ->line('**Data:** '.($booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') : 'Não informada'))
            ->line('**Horário Início:** '.($booking->start_time ?: 'Não informado'))
            ->line('Prepare-se e chegue no horário!')
            ->salutation('Atenciosamente, '.$booking->field->company->name);
    }
}
