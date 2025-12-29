<?php

namespace App\Utils;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Normaliza uma data para o formato Y-m-d
     */
    public static function normalizeDate(string $date): string
    {
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Data inválida: '.$date);
        }
    }

    /**
     * Calcula a idade baseada na data de nascimento
     */
    public static function calculateAge(string $birthDate): int
    {
        return Carbon::parse($birthDate)->age;
    }

    /**
     * Verifica se a pessoa tem idade mínima
     */
    public static function hasMinimumAge(string $birthDate, int $minimumAge = 14): bool
    {
        return self::calculateAge($birthDate) >= $minimumAge;
    }
}
