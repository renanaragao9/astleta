<?php

namespace App\Services\Pdf;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class BookingPdfService
{
    public function generateBookingReceiptPdf(Booking $booking): string
    {
        $booking->load(['user', 'field.company', 'paymentForm']);

        $pdf = Pdf::loadView('pdf.booking-receipt', ['booking' => $booking])
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

        $filename = "recibo_reserva_{$booking->id}_".now()->format('YmdHis').'.pdf';
        $filepath = storage_path('app/temp/'.$filename);

        $this->ensureDirectoryExists(dirname($filepath));

        $pdf->save($filepath);

        return $filepath;
    }

    public function generateBookingReceiptPdfContent(Booking $booking): string
    {
        $booking->load(['user', 'field.company', 'paymentForm']);

        $pdf = Pdf::loadView('pdf.booking-receipt', ['booking' => $booking])
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

    private function ensureDirectoryExists(string $directory): void
    {
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }

    public function cleanBookingReceiptPdfFiles(int $bookingId): void
    {
        $pattern = storage_path('app/temp/recibo_reserva_'.$bookingId.'_*.pdf');
        $files = glob($pattern);

        foreach ($files as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }
    }
}
