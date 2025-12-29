<?php

namespace App\Services\Pdf;

use App\Models\Tab;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class TabPdfService
{
    /**
     * Gera PDF da comanda com todos os detalhes
     */
    public function generateTabPdf(Tab $tab): string
    {
        $tab->load(['tabItems.product', 'company', 'paymentForm']);

        $pdf = Pdf::loadView('pdf.tab-invoice', ['tab' => $tab])
            ->setPaper([0, 0, 226.77, 1000], 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Courier New',
                'margin_top' => 5,
                'margin_right' => 5,
                'margin_bottom' => 5,
                'margin_left' => 5,
                'dpi' => 96,
                'isRemoteEnabled' => false,
            ]);

        $filename = "nota_comanda_{$tab->code}_".now()->format('YmdHis').'.pdf';
        $filepath = storage_path('app/temp/'.$filename);

        $this->ensureDirectoryExists(dirname($filepath));

        $pdf->save($filepath);

        return $filepath;
    }

    /**
     * Gera o PDF e retorna o conteúdo em string para anexar em email
     */
    public function generateTabPdfContent(Tab $tab): string
    {
        $tab->load(['tabItems.product', 'company', 'paymentForm']);

        $pdf = Pdf::loadView('pdf.tab-invoice', ['tab' => $tab])
            ->setPaper([0, 0, 226.77, 1000], 'portrait') // 80mm width (226.77 points)
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Courier New',
                'margin_top' => 5,
                'margin_right' => 5,
                'margin_bottom' => 5,
                'margin_left' => 5,
                'dpi' => 96,
                'isRemoteEnabled' => false,
            ]);

        return $pdf->output();
    }

    /**
     * Gera o PDF de comprovante de comanda
     */
    public function generateTabReceiptPdf(Tab $tab): string
    {
        $tab->load(['tabItems.product', 'company', 'paymentForm']);

        $pdf = Pdf::loadView('pdf.tab-receipt', ['tab' => $tab])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'margin_top' => 10,
                'margin_right' => 10,
                'margin_bottom' => 10,
                'margin_left' => 10,
                'dpi' => 96,
                'isRemoteEnabled' => false,
            ]);

        $filename = "comprovante_comanda_{$tab->code}_".now()->format('YmdHis').'.pdf';
        $filepath = storage_path('app/temp/'.$filename);

        $this->ensureDirectoryExists(dirname($filepath));

        $pdf->save($filepath);

        return $filepath;
    }

    /**
     * Gera o PDF de comprovante de comanda e retorna o conteúdo em string
     */
    public function generateTabReceiptPdfContent(Tab $tab): string
    {
        $tab->load(['tabItems.product', 'company', 'paymentForm']);

        $pdf = Pdf::loadView('pdf.tab-receipt', ['tab' => $tab])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'margin_top' => 10,
                'margin_right' => 10,
                'margin_bottom' => 10,
                'margin_left' => 10,
                'dpi' => 96,
                'isRemoteEnabled' => false,
            ]);

        return $pdf->output();
    }

    public function cleanTabPdfFiles(string $tabCode): void
    {
        $tempDir = storage_path('app/temp/');
        if (is_dir($tempDir)) {
            $files = glob($tempDir."nota_comanda_{$tabCode}_*.pdf");
            foreach ($files as $file) {
                if (File::exists($file)) {
                    File::delete($file);
                }
            }
        }
    }

    public function cleanOldPdfFiles(int $hoursOld = 24): void
    {
        $tempDir = storage_path('app/temp/');
        if (is_dir($tempDir)) {
            $files = glob($tempDir.'nota_comanda_*.pdf');
            $cutoffTime = now()->subHours($hoursOld)->timestamp;

            foreach ($files as $file) {
                if (File::exists($file) && filemtime($file) < $cutoffTime) {
                    File::delete($file);
                }
            }
        }
    }

    private function ensureDirectoryExists(string $directory): void
    {
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }
}
