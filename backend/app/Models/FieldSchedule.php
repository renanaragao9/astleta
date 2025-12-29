<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldSchedule extends BaseModel
{
    protected $fillable = [
        'field_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function getDayOfWeekNameAttribute(): string
    {
        $days = [
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
            7 => 'Domingo',
        ];

        return $days[$this->day_of_week] ?? 'Dia inválido';
    }

    public function getFormattedScheduleAttribute(): string
    {
        return $this->day_of_week_name.' - '.substr($this->start_time, 0, 5).' às '.substr($this->end_time, 0, 5);
    }
}
