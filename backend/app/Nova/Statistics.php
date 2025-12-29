<?php

namespace App\Nova;

use App\Models\Statistics as StatisticsModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Statistics extends Resource
{
    public static $model = StatisticsModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
    ];

    public static function label(): string
    {
        return 'Estatísticas';
    }

    public static function singularLabel(): string
    {
        return 'Estatística';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Nome', 'name')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Digite o nome da estatística.'),

            Text::make('Ícone', 'icon')
                ->sortable()
                ->rules('nullable', 'max:255')
                ->help('Digite a classe do ícone (ex: pi pi-flag, pi pi-users).'),

            Text::make('Cor', 'color')
                ->sortable()
                ->rules('nullable', 'max:255')
                ->help('Digite a cor (ex: primary, green-600, blue-500).'),

            BelongsTo::make('Esporte', 'sport', Sport::class)
                ->rules('required')
                ->help('Selecione o esporte associado.'),

            BelongsTo::make('Posição', 'position', Position::class)
                ->nullable()
                ->help('Selecione a posição associada (opcional).'),
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
