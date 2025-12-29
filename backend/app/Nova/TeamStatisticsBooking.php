<?php

namespace App\Nova;

use App\Models\TeamStatisticsBooking as TeamStatisticsBookingModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;

class TeamStatisticsBooking extends Resource
{
    public static $model = TeamStatisticsBookingModel::class;

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public static function label(): string
    {
        return 'Estatísticas de Reservas de Time';
    }

    public static function singularLabel(): string
    {
        return 'Estatística de Reserva de Time';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Number::make('Contagem', 'count')
                ->default(0)
                ->rules('required', 'integer', 'min:0')
                ->help('Número de ocorrências da estatística.'),

            BelongsTo::make('Time', 'team', Team::class)
                ->rules('required')
                ->help('Time associado à estatística.')
                ->sortable(),

            BelongsTo::make('Reserva de Time', 'teamBooking', TeamBooking::class)
                ->rules('required')
                ->help('Reserva de time relacionada.')
                ->sortable(),

            BelongsTo::make('Estatística de Time', 'statisticTeam', StatisticsTeam::class)
                ->rules('required')
                ->help('Estatística específica.')
                ->sortable(),

            BelongsTo::make('Reserva', 'booking', Booking::class)
                ->rules('required')
                ->help('Reserva geral associada.')
                ->sortable(),
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
}
