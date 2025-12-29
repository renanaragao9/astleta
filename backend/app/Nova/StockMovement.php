<?php

namespace App\Nova;

use App\Models\StockMovement as StockMovementModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class StockMovement extends Resource
{
    public static $model = StockMovementModel::class;

    public static $title = 'id';

    public static $search = [
        'id',
        'type',
        'status',
    ];

    public static function label(): string
    {
        return 'Movimentações de Estoque';
    }

    public static function singularLabel(): string
    {
        return 'Movimentação de Estoque';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Select::make('Tipo', 'type')
                ->options([
                    'entrada' => 'Entrada',
                    'saida' => 'Saída',
                    'ajuste' => 'Ajuste',
                ])
                ->help('Selecione o tipo da movimentação.'),

            Select::make('Status', 'status')
                ->options([
                    'pendente' => 'Pendente',
                    'aprovada' => 'Aprovada',
                    'cancelada' => 'Cancelada',
                ])
                ->help('Selecione o status da movimentação.'),

            BelongsTo::make('Estoque', 'stock', Stock::class)
                ->rules('required')
                ->help('Selecione o estoque relacionado.'),

            MorphTo::make('Movimentável', 'movimentable')
                ->types([
                    Purchase::class,
                ])
                ->help('Selecione o item movimentável.'),
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
        return "/resources/stock-movements";
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource): string
    {
        return "/resources/stock-movements";
    }
}