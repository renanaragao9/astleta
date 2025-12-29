<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TimeDifferenceRule implements ValidationRule
{
    protected $startTime;

    public function __construct($startTime)
    {
        $this->startTime = $startTime;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $start = Carbon::createFromFormat('H:i', $this->startTime);
        $end = Carbon::createFromFormat('H:i', $value);

        $diffInMinutes = $start->diffInMinutes($end);

        if ($diffInMinutes < 60 || $diffInMinutes % 30 !== 0) {
            $fail('A diferença entre o horário de início e fim deve ser de pelo menos 1 hora e em múltiplos de 30 minutos.');
        }
    }
}
