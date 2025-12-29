<?php

namespace App\Nova;

use App\Models\TeamBooking as TeamBookingModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class TeamBooking extends Resource
{
    public static $model = TeamBookingModel::class;

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public static function label(): string
    {
        return 'Reservas de Time';
    }

    public static function singularLabel(): string
    {
        return 'Reserva de Time';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Select::make('Resultado', 'result')
                ->options([
                    'vitoria' => 'Vitória',
                    'empate' => 'Empate',
                    'derrota' => 'Derrota',
                ])
                ->nullable()
                ->displayUsingLabels()
                ->help('Resultado da partida.'),

            Number::make('Gols Casa', 'goals_home')
                ->default(0)
                ->rules('required', 'integer', 'min:0')
                ->help('Número de gols do time da casa.'),

            Number::make('Gols Fora', 'goals_away')
                ->default(0)
                ->rules('required', 'integer', 'min:0')
                ->help('Número de gols do time visitante.'),

            Boolean::make('Amistoso', 'is_friendly')
                ->default(true)
                ->help('Define se a partida é amistosa.'),

            BelongsTo::make('Time da Casa', 'homeTeam', Team::class)
                ->rules('required')
                ->help('Time que joga em casa.')
                ->sortable(),

            BelongsTo::make('Time Visitante', 'awayTeam', Team::class)
                ->rules('required')
                ->help('Time visitante.')
                ->sortable(),

            BelongsTo::make('Reserva', 'booking', Booking::class)
                ->rules('required')
                ->help('Reserva associada à partida.')
                ->sortable(),

            BelongsTo::make('Esporte', 'sport', Sport::class)
                ->nullable()
                ->help('Esporte da partida.')
                ->sortable(),

            BelongsTo::make('Vencedor', 'winner', Team::class)
                ->nullable()
                ->help('Time vencedor da partida.')
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
