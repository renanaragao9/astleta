<?php

namespace App\Notifications;

use App\Models\TeamPlayer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamPlayerWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected TeamPlayer $teamPlayer;

    public function __construct(TeamPlayer $teamPlayer)
    {
        $this->teamPlayer = $teamPlayer;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $teamPlayer = $this->teamPlayer;
        $team = $teamPlayer->team;

        $subject = "Bem-vindo ao Time {$team->name}";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Você foi adicionado ao time ' . $team->name . '.')
            ->line('Sua função no time: ' . ucfirst($teamPlayer->role))
            ->line('Número da camisa: ' . ($teamPlayer->number ?? 'Não definido'))
            ->line($team->welcome_email)
            ->line('Boa sorte nas suas partidas!')
            ->salutation("Atenciosamente, {$team->name}");

        return $mail;
    }
}