<?php

namespace App\Services\Api\Company\Tab;

use App\Models\Tab;
use App\Models\User;
use App\Notifications\MessageNotification;
use App\Notifications\TabSent;
use App\Services\Pdf\TabPdfService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendService
{
    public $user;

    public function run(array $data): array
    {
        $tab = Tab::with(['tabItems.product', 'paymentForm'])->findOrFail($data['tabId']);

        if ($tab->status === 'cancelado') {
            throw new \Exception('Não é possível enviar comandas canceladas.');
        }

        return match ($data['sendMethod']) {
            'email' => $this->sendToEmail($tab, $data['email']),
            'sistema' => $this->sendToUser($tab, $data['cpf']),
            default => throw new \Exception('Método de envio inválido.')
        };
    }

    private function sendToEmail(Tab $tab, string $email): array
    {
        try {
            $this->sendWithPdf($tab, function () use ($tab, $email) {
                Notification::route('mail', $email)->notify(new TabSent($tab));
            });

            return [
                'message' => 'Comanda enviada com sucesso por email com PDF anexado.',
                'tab_id' => $tab->id,
                'method' => 'email',
                'recipient' => $email,
                'pdf_generated' => true,
            ];
        } catch (\Exception $e) {
            $this->user->createNotificationMessage(get_class($this), (new MessageNotification)
                ->message('Erro ao enviar comanda: '.$e->getMessage())
                ->icon('pi-exclamation-triangle')
                ->toArray());

            throw $e;
        }
    }

    private function sendToUser(Tab $tab, string $cpf): array
    {
        $userSystem = User::where('cpf', $cpf)->first()
            ?? throw new \Exception('Usuário não encontrado com o CPF informado.');

        if (! $userSystem->email) {
            throw new \Exception('Usuário não possui email cadastrado.');
        }

        $tab->update(['user_id' => $userSystem->id]);

        try {
            $this->sendWithPdf($tab, fn () => $userSystem->notify(new TabSent($tab)));

            $userSystem->createNotificationMessage(get_class($this), (new MessageNotification)
                ->message('Olá, sua comanda foi recebida com sucesso. Verifique em "Minhas Comandas" para mais detalhes.')
                ->icon('pi-check-circle')
                ->toArray());
        } catch (\Exception $e) {
            $this->user->createNotificationMessage(get_class($this), (new MessageNotification)
                ->message('Erro ao enviar comanda: '.$e->getMessage())
                ->icon('pi-exclamation-triangle')
                ->toArray());

            throw $e;
        }

        return [
            'message' => 'Comanda enviada com sucesso para o usuário do sistema com PDF anexado.',
            'tab_id' => $tab->id,
            'method' => 'sistema',
            'user_id' => $userSystem->id,
            'recipient' => $userSystem->email,
            'pdf_generated' => true,
        ];
    }

    private function sendWithPdf(Tab $tab, callable $sendCallback): void
    {
        try {
            $pdfService = app(TabPdfService::class);
            $pdfService->generateTabPdf($tab);

            $sendCallback();

            $pdfService->cleanTabPdfFiles($tab->code);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar comanda com PDF', [
                'tab_id' => $tab->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
