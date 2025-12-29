<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Booking $booking;

    protected string $type;

    protected bool $isOwner;

    public function __construct(Booking $booking, string $type, bool $isOwner = false)
    {
        $this->booking = $booking;
        $this->type = $type;
        $this->isOwner = $isOwner;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking;
        $totalAmount = number_format((float) $booking->total_amount, 2, ',', '.');

        $subject = $this->type === 'created'
            ? "Reserva {$booking->booking_number} - {$booking->field->name}"
            : "Reserva {$booking->booking_number} - {$booking->field->name} - Status Atualizado";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line($this->type === 'created'
                ? ($this->isOwner
                    ? 'Uma nova reserva foi criada para o seu campo. Segue abaixo os detalhes:'
                    : 'Sua reserva foi recebida com sucesso. Segue abaixo os detalhes:')
                : 'Sua reserva foi atualizada. Segue abaixo os detalhes:')
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

        $mail->line('**Observação:** Acompanhe o status e detalhes da sua reserva em "Minhas Reservas".')
            ->line('Obrigado por utilizar nossos serviços!')
            ->salutation('Atenciosamente, '.$booking->field->company->name);

        return $mail;
    }
}
