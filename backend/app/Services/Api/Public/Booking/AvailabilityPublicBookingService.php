<?php

namespace App\Services\Api\Public\Booking;

use App\Models\Booking;
use App\Models\Field;
use App\Models\FieldSchedule;
use Carbon\Carbon;

class AvailabilityPublicBookingService extends BaseService
{
    public function run(array $data): array
    {
        $fieldId = $data['field_id'];
        $date = $data['date'];

        $field = Field::where('id', $fieldId)
            ->first();

        $selectedDate = Carbon::parse($date);
        $dayOfWeek = $selectedDate->dayOfWeekIso;

        $schedules = FieldSchedule::query()
            ->where('field_id', $fieldId)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time', 'asc')
            ->get();

        if ($schedules->isEmpty()) {
            return [
                'available_slots' => [],
                'message' => 'Campo não possui horários configurados para este dia da semana.',
            ];
        }

        $existingBookings = Booking::where('field_id', $fieldId)
            ->where('booking_date', $selectedDate->toDateString())
            ->where('booking_status', '!=', 'cancelado')
            ->get();

        $availableSlots = [];

        foreach ($schedules as $schedule) {
            $startTime = Carbon::parse($schedule->start_time);
            $endTime = Carbon::parse($schedule->end_time);

            while ($startTime->copy()->addHour() <= $endTime) {
                $slotStart = $startTime->format('H:i');
                $slotEnd = $startTime->copy()->addHour()->format('H:i');

                $isOccupied = $existingBookings->contains(function ($booking) use ($slotStart, $slotEnd) {
                    $bookingStart = Carbon::parse($booking->start_time)->format('H:i');
                    $bookingEnd = Carbon::parse($booking->end_time)->format('H:i');

                    return $bookingStart < $slotEnd && $bookingEnd > $slotStart;
                });

                if (! $isOccupied) {
                    $availableSlots[] = [
                        'startTime' => $slotStart,
                        'endTime' => $slotEnd,
                        'durationMinutes' => 60,
                        'price' => $field->price_per_hour,
                    ];
                }

                $startTime->addHour();
            }
        }

        return [
            'field' => [
                'id' => $field->id,
                'name' => $field->name,
                'pricePerHour' => $field->price_per_hour,
                'extraHourPrice' => $field->extra_hour_price,
                'allowsExtraHour' => $field->is_allows_extra_hour,
            ],
            'date' => $selectedDate->format('Y-m-d'),
            'dayOfWeek' => $dayOfWeek,
            'availableSlots' => $availableSlots,
            'totalSlots' => count($availableSlots),
        ];
    }
}
