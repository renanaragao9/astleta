<?php

namespace App\Helpers;

class TimeHelper
{
    /**
     * Gera opções de horário com intervalo customizável em minutos.
     *
     * @param  int  $intervalMinutes  Intervalo em minutos (ex: 15, 30, 60)
     * @return array Array associativo com opções de horário ['HH:MM' => 'HH:MM']
     */
    public static function generateTimeOptions(int $intervalMinutes = 60): array
    {
        $options = [];
        $totalMinutesInDay = 24 * 60;

        for ($minutes = 0; $minutes < $totalMinutesInDay; $minutes += $intervalMinutes) {
            $hours = floor($minutes / 60);
            $mins = $minutes % 60;
            $time = sprintf('%02d:%02d:00', $hours, $mins);
            $label = sprintf('%02d:%02d', $hours, $mins);
            $options[$time] = $label;
        }

        return $options;
    }
}
