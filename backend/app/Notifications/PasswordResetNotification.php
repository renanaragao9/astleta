<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $token,
        public string $email
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = config('app.frontend_url').'/redefinir-senha?email='.urlencode($this->email).'&token='.$this->token;

        return (new MailMessage)
            ->subject('Redefina sua senha - '.config('app.name'))
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Você solicitou a redefinição de senha para sua conta no '.config('app.name').'.')
            ->action('Redefinir Senha', $resetUrl)
            ->line('Este link expira em 60 minutos.')
            ->line('Se você não solicitou a redefinição, ignore este e-mail.')
            ->salutation('Atenciosamente, equipe '.config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->token,
            'user_id' => $notifiable->id,
        ];
    }
}
