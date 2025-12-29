<?php

namespace App\Notifications;

use App\Models\Tab;
use App\Services\Pdf\TabPdfService;
use App\Traits\CleansTemporaryFiles;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TabSent extends Notification implements ShouldQueue
{
    use CleansTemporaryFiles, Queueable;

    protected Tab $tab;

    public function __construct(Tab $tab)
    {
        $this->tab = $tab;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tab = $this->tab;
        $totalAmount = number_format((float) $tab->total_amount, 2, ',', '.');

        $pdfService = app(TabPdfService::class);
        $pdfPath = $pdfService->generateTabPdf($tab);
        $pdfFilename = "Nota_Comanda_{$tab->code}.pdf";

        $mail = (new MailMessage)
            ->subject("Nota de Comanda {$tab->code} - {$tab->customer_name}")
            ->greeting('Olá, '.$tab->customer_name.'!')
            ->line('Segue em anexo a nota detalhada da sua comanda.')
            ->line("**Código:** {$tab->code}")
            ->line("**Cliente:** {$tab->customer_name}")
            ->line('**Status:** '.ucfirst($tab->status))
            ->line('**Data de Abertura:** '.$tab->opened_at->format('d/m/Y H:i'))
            ->line("**Valor Total:** R$ {$totalAmount}")
            ->line('**Nota não fiscal em anexo com todos os itens e informações da comanda.**')
            ->line('Obrigado por utilizar nossos serviços!')
            ->salutation('Atenciosamente, '.($tab->company->name ?? 'Equipe da Empresa'))
            ->attach($pdfPath, [
                'as' => $pdfFilename,
                'mime' => 'application/pdf',
            ]);

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tab_id' => $this->tab->id,
            'tab_code' => $this->tab->code,
            'customer_name' => $this->tab->customer_name,
            'total_amount' => $this->tab->total_amount,
            'status' => $this->tab->status,
        ];
    }

    public function sent($notifiable, $channel)
    {
        $pdfService = app(TabPdfService::class);
        $pdfService->cleanTabPdfFiles($this->tab->code);
    }
}
