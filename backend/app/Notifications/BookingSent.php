<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingSent extends Notification implements ShouldQueue
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
        $totalAmount = number_format((float) $booking->total_amount, 2, ',', '.');

        $mail = (new MailMessage)
            ->subject("Reserva {$booking->booking_number} - {$booking->field->name}")
            ->greeting('Olá!')
            ->line('Segue abaixo os detalhes da sua reserva:')
            ->line('**Número da Reserva:** '.($booking->booking_number ?: 'Não informado'))
            ->line("**Campo:** {$booking->field->name}")
            ->line('**Status:** '.ucfirst($booking->booking_status ?: 'Não informado'))
            ->line('**Data:** '.($booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') : 'Não informada'))
            ->line('**Horário Início:** '.($booking->start_time ?: 'Não informado'))
            ->line('**Horário Fim:** '.($booking->end_time ?: 'Não informado'))
            ->line('**Duração:** '.($booking->duration_minutes ?: 0).' minutos')
            ->line("**Valor Total:** R$ {$totalAmount}");

        if ($booking->paymentForm) {
            $mail->line("**Forma de Pagamento:** {$booking->paymentForm->name}");
        }

        if ($booking->notes) {
            $mail->line("**Observações:** {$booking->notes}");
        }

        $mail->line('Obrigado por utilizar nossos serviços!')
            ->salutation('Atenciosamente, '.$booking->field->company->name);

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->bookingNumber,
            'field_name' => $this->booking->field->name,
            'total_amount' => $this->booking->totalAmount,
            'status' => $this->booking->bookingStatus,
        ];
    }
}
