<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Notifications\BookingReminderNotification;
use App\Notifications\MessageNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyUpcomingBookings extends Command
{
    protected $signature = 'app:notify-upcoming-bookings';

    protected $description = 'Notifica usuários 30 minutos antes do início de suas reservas';

    public function handle()
    {
        $now = Carbon::now();
        $thirtyMinutesFromNow = $now->copy()->addMinutes(30);

        $bookings = Booking::where('booking_date', $now->toDateString())
            ->where('start_time', '>=', $now->format('H:i:s'))
            ->where('start_time', '<=', $thirtyMinutesFromNow->format('H:i:s'))
            ->where('booking_status', 'confirmado')
            ->get();

        $delay = 0;
        foreach ($bookings as $booking) {
            $message = "Falta 30 minutos para começar sua reserva {$booking->booking_number}. Prepare-se!";
            $booking->user->createNotificationMessage(
                get_class($booking),
                (new MessageNotification)
                    ->message($message)
                    ->icon('pi-clock')
                    ->toArray()
            );

            $booking->user->notify((new BookingReminderNotification($booking))->delay(now()->addSeconds($delay)));
            $delay += 1;

            $this->info("Notificação enviada para reserva {$booking->booking_number}");
        }

        $this->info("Total de reservas notificadas: {$bookings->count()}");
    }
}
