<?php

namespace App\Nova;

use App\Models\TeamPlayer as TeamPlayerModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class TeamPlayer extends Resource
{
    public static $model = TeamPlayerModel::class;

    public static $title = 'id';

    public static $search = [
        'id',
        'number',
    ];

    public static function label(): string
    {
        return 'Jogadores da Equipe';
    }

    public static function singularLabel(): string
    {
        return 'Jogador da Equipe';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Equipe', 'team', Team::class)
                ->rules('required')
                ->help('Equipe à qual o jogador pertence.')
                ->sortable(),

            BelongsTo::make('Usuário', 'user', User::class)
                ->rules('required')
                ->help('Usuário que é jogador da equipe.')
                ->sortable(),

            Number::make('Número', 'number')
                ->nullable()
                ->rules('nullable', 'integer', 'min:1')
                ->help('Número do jogador na equipe.'),

            Select::make('Função', 'role')
                ->options([
                    'jogador' => 'Jogador',
                    'capitao' => 'Capitão',
                    'treinador' => 'Treinador',
                ])
                ->default('jogador')
                ->rules('required')
                ->help('Função do jogador na equipe.')
                ->sortable(),

            Select::make('Status', 'status')
                ->options([
                    'pendente' => 'Pendente',
                    'ativo' => 'Ativo',
                    'rescindido' => 'Rescindido',
                ])
                ->default('ativo')
                ->rules('required')
                ->help('Status do jogador na equipe.')
                ->sortable(),

            Date::make('Data de Entrada', 'joined_at')
                ->nullable()
                ->help('Data em que o jogador entrou na equipe.')
                ->sortable(),

            Date::make('Data de Saída', 'left_at')
                ->nullable()
                ->help('Data em que o jogador saiu da equipe.')
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
