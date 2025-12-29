<?php

namespace App\Nova;

use App\Models\TournamentTeam as TournamentTeamModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;

class TournamentTeam extends Resource
{
    public static $model = TournamentTeamModel::class;

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public static function label(): string
    {
        return 'Equipes de Torneios';
    }

    public static function singularLabel(): string
    {
        return 'Equipe de Torneio';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Torneio', 'tournament', Tournament::class)
                ->rules('required')
                ->help('Selecione o torneio.'),

            BelongsTo::make('Equipe', 'team', Team::class)
                ->rules('required')
                ->help('Selecione a equipe.'),

            Number::make('Pontos', 'points')
                ->default(0)
                ->rules('integer', 'min:0')
                ->help('Pontos da equipe.'),

            Number::make('Posição', 'position')
                ->nullable()
                ->rules('nullable', 'integer', 'min:1')
                ->help('Posição da equipe (opcional).'),

            Number::make('Vitórias', 'wins')
                ->default(0)
                ->rules('integer', 'min:0')
                ->help('Número de vitórias.'),

            Number::make('Empates', 'draws')
                ->default(0)
                ->rules('integer', 'min:0')
                ->help('Número de empates.'),

            Number::make('Derrotas', 'losses')
                ->default(0)
                ->rules('integer', 'min:0')
                ->help('Número de derrotas.'),
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
        return "/resources/tournaments/{$resource->tournament_id}";
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource): string
    {
        return "/resources/tournaments/{$resource->tournament_id}";
    }
}