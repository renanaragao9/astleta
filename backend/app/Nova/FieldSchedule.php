<?php

namespace App\Nova;

use App\Helpers\TimeHelper;
use App\Models\FieldSchedule as FieldScheduleModel;
use App\Rules\UniqueFieldScheduleTime;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class FieldSchedule extends Resource
{
    public static $model = FieldScheduleModel::class;

    public static $title = 'formatted_schedule';

    public static $search = [
        'id',
    ];

    public static function label(): string
    {
        return 'Horários dos Campos';
    }

    public static function singularLabel(): string
    {
        return 'Horário do Campo';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Select::make('Dia da Semana', 'day_of_week')
                ->options([
                    1 => 'Segunda-feira',
                    2 => 'Terça-feira',
                    3 => 'Quarta-feira',
                    4 => 'Quinta-feira',
                    5 => 'Sexta-feira',
                    6 => 'Sábado',
                    7 => 'Domingo',
                ])
                ->displayUsingLabels()
                ->rules('required', 'integer', 'between:1,7')
                ->help('Escolha o dia da semana para este horário de funcionamento.')
                ->sortable(),

            Select::make('Horário de Início', 'start_time')
                ->dependsOn('interval_minutes', function (Select $field, NovaRequest $request, $formData) {
                    $interval = $formData['interval_minutes'] ?? 60;

                    return $field->options(TimeHelper::generateTimeOptions($interval));
                })
                ->rules('required')
                ->displayUsing(fn ($value) => $value ? substr($value, 0, 5) : '')
                ->help('Defina o horário de início do período disponível para reservas neste dia.')
                ->sortable(),

            Select::make('Horário de Fim', 'end_time')
                ->dependsOn(['interval_minutes', 'start_time'], function (Select $field, NovaRequest $request, $formData) {
                    $interval = $formData['interval_minutes'] ?? 60;
                    $startTime = $formData['start_time'] ?? null;
                    $options = TimeHelper::generateTimeOptions($interval);
                    if ($startTime) {
                        $filteredOptions = [];
                        foreach ($options as $time => $label) {
                            if ($time > $startTime) {
                                $filteredOptions[$time] = $label;
                            }
                        }
                        $options = $filteredOptions;
                    }

                    return $field->options($options);
                })
                ->rules('required', function ($attribute, $value, $fail) use ($request) {
                    $startTime = $request->input('start_time');
                    if ($startTime && $value <= $startTime) {
                        $fail('O horário de fim deve ser posterior ao horário de início.');
                    }

                    $rule = new UniqueFieldScheduleTime(
                        $request->input('day_of_week'),
                        $request->input('field'),
                        $startTime,
                        $request->route('resourceId')
                    );

                    if (! $rule->passes($attribute, $value)) {
                        $fail($rule->message());
                    }
                })
                ->displayUsing(fn ($value) => $value ? substr($value, 0, 5) : '')
                ->help('Defina o horário de fim do período disponível. Deve ser posterior ao horário de início.'),

            BelongsTo::make('Campo', 'field', Field::class)
                ->rules('required')
                ->help('Selecione o campo esportivo para o qual este horário será aplicado.'),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [];
    }

    public static function redirectAfterCreate(NovaRequest $request, $resource): string
    {
        return "/resources/fields/{$resource->field_id}";
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource): string
    {
        return "/resources/fields/{$resource->field_id}";
    }
}
