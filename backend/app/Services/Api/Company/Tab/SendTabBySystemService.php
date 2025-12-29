<?php

namespace App\Services\Api\Company\Tab;

use App\Models\Tab;
use App\Models\User;
use App\Notifications\MessageNotification;
use App\Notifications\TabSent;

class SendTabBySystemService
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function run(int $tabId, string $phone): bool
    {
        $tab = Tab::with(['company', 'paymentForm'])->findOrFail($tabId);

        if ($tab->status === 'cancelado') {
            throw new \Exception('Não é possível enviar comandas canceladas.');
        }

        try {
            $userSystem = User::where('phone', $phone)->first();

            if (! $userSystem) {
                throw new \Exception('Usuário não encontrado com o telefone informado.');
            }

            $tab->update(['user_id' => $userSystem->id]);

            $userSystem->notify(new TabSent($tab));

            $userSystem->createNotificationMessage(get_class($this), (new MessageNotification)
                ->message('Olá, sua comanda foi recebida com sucesso. Verifique em "Minhas Comandas" para mais detalhes.')
                ->icon('pi-check-circle')
                ->toArray());

            return true;

        } catch (\Exception $e) {
            $this->user->createNotificationMessage(get_class($this), (new MessageNotification)
                ->message('Erro ao enviar comanda: '.$e->getMessage())
                ->icon('pi-exclamation-triangle')
                ->toArray());
            throw new \Exception('Erro ao enviar para usuário do sistema: '.$e->getMessage());
        }
    }
}
