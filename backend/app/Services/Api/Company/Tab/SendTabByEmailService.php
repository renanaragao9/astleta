<?php

namespace App\Services\Api\Company\Tab;

use App\Models\Tab;
use App\Models\User;
use App\Notifications\MessageNotification;
use App\Notifications\TabSent;
use Illuminate\Support\Facades\Notification;

class SendTabByEmailService
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function run(int $tabId, string $email): bool
    {
        $tab = Tab::with(['company', 'paymentForm'])->findOrFail($tabId);

        if ($tab->status === 'cancelado') {
            throw new \Exception('Não é possível enviar comandas canceladas.');
        }

        try {
            Notification::route('mail', $email)->notify(new TabSent($tab));

            return true;
        } catch (\Exception $e) {
            $this->user->createNotificationMessage(get_class($this), (new MessageNotification)
                ->message('Erro ao enviar comanda por email: '.$e->getMessage())
                ->icon('pi-exclamation-triangle')
                ->toArray());

            throw new \Exception('Erro ao enviar email: '.$e->getMessage());
        }
    }
}
