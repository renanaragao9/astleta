<?php

namespace App\Services\Api\Company\Field;

use App\Models\Field;
use Illuminate\Validation\ValidationException;

class SyncSchedulesService
{
    public function sync(Field $field, array $schedules): void
    {
        $this->validateSchedules($schedules);

        $field->fieldSchedules()->delete();

        $normalizedSchedules = collect($schedules)->map(fn ($schedule) => [
            'day_of_week' => $schedule['day_of_week'],
            'start_time' => $schedule['start_time'],
            'end_time' => $schedule['end_time'],
        ])->toArray();

        if (! empty($normalizedSchedules)) {
            $field->fieldSchedules()->createMany($normalizedSchedules);
        }
    }

    protected function validateSchedules(array $schedules): void
    {
        $groupedByDay = collect($schedules)->groupBy('day_of_week');

        foreach ($groupedByDay as $day => $entries) {
            $intervals = $entries->map(fn ($entry) => [
                'start' => strtotime($entry['start_time']),
                'end' => strtotime($entry['end_time']),
            ]);

            for ($i = 0; $i < count($intervals); $i++) {
                for ($j = $i + 1; $j < count($intervals); $j++) {
                    $a = $intervals[$i];
                    $b = $intervals[$j];

                    if ($a['start'] < $b['end'] && $b['start'] < $a['end']) {
                        throw ValidationException::withMessages([
                            'schedules' => "Conflito de horário no dia {$day} entre {$entries[$i]['start_time']} - {$entries[$i]['end_time']} e {$entries[$j]['start_time']} - {$entries[$j]['end_time']}.",
                        ]);
                    }
                }
            }
        }
    }
}
