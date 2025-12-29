<?php

namespace App\Nova;

use App\Models\PlayerStatistic as PlayerStatisticModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;

class PlayerStatistic extends Resource
{
    public static $model = PlayerStatisticModel::class;

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public static function label(): string
    {
        return 'Estatísticas de Jogadores';
    }

    public static function singularLabel(): string
    {
        return 'Estatística de Jogador';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Number::make('Contagem', 'count')
                ->min(0)
                ->step(1)
                ->sortable(),

            BelongsTo::make('Usuário', 'user', User::class)
                ->searchable()
                ->sortable(),

            BelongsTo::make('Estatística', 'statistic', Statistics::class)
                ->searchable()
                ->sortable(),

            BelongsTo::make('Reserva', 'booking', Booking::class)
                ->searchable()
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
