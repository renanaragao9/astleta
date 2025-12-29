<?php

namespace App\Nova;

use App\Models\StatisticsTeam as StatisticsTeamModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class StatisticsTeam extends Resource
{
    public static $model = StatisticsTeamModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
    ];

    public static function label(): string
    {
        return 'Estatísticas de Time';
    }

    public static function singularLabel(): string
    {
        return 'Estatística de Time';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Nome', 'name')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Nome da estatística.'),

            Text::make('Ícone', 'icon')
                ->nullable()
                ->hideFromIndex()
                ->help('Ícone representativo da estatística.'),

            Text::make('Cor', 'color')
                ->nullable()
                ->hideFromIndex()
                ->help('Cor associada à estatística.'),

            BelongsTo::make('Esporte', 'sport', Sport::class)
                ->rules('required')
                ->help('Esporte relacionado à estatística.')
                ->sortable(),

            HasMany::make('Estatísticas de Reservas de Time', 'teamStatisticsBookings', TeamStatisticsBooking::class),
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
