<?php

namespace App\Rules;

use App\Models\FieldSchedule as FieldScheduleModel;
use Illuminate\Contracts\Validation\Rule;

class UniqueFieldScheduleTime implements Rule
{
    protected $dayOfWeek;

    protected $fieldId;

    protected $startTime;

    protected $resourceId;

    public function __construct($dayOfWeek, $fieldId, $startTime, $resourceId = null)
    {
        $this->dayOfWeek = $dayOfWeek;
        $this->fieldId = $fieldId;
        $this->startTime = $startTime;
        $this->resourceId = $resourceId;
    }

    public function passes($attribute, $value)
    {
        $query = FieldScheduleModel::where('field_id', $this->fieldId)
            ->where('day_of_week', $this->dayOfWeek)
            ->where('start_time', $this->startTime)
            ->where('end_time', $value);

        if ($this->resourceId) {
            $query->where('id', '!=', $this->resourceId);
        }

        return ! $query->exists();
    }

    public function message()
    {
        return 'Já existe um horário idêntico para este campo e dia da semana.';
    }
}
