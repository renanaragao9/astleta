<?php

namespace App\Notifications;

use App\Models\TournamentTeam;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TournamentWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected TournamentTeam $tournamentTeam;

    public function __construct(TournamentTeam $tournamentTeam)
    {
        $this->tournamentTeam = $tournamentTeam;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tournamentTeam = $this->tournamentTeam;
        $tournament = $tournamentTeam->tournament;

        $subject = "Bem-vindo ao Torneio {$tournament->name}";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Seu time foi inscrito com sucesso no torneio.')
            ->line($tournament->welcome_email)
            ->line('Boa sorte no torneio!')
            ->salutation('Atenciosamente, ' . $tournament->company->name);

        return $mail;
    }
}