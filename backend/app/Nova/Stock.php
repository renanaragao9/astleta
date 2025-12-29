<?php

namespace App\Nova;

use App\Models\Stock as StockModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Stock extends Resource
{
    public static $model = StockModel::class;

    public static $title = 'id';

    public static $search = [
        'id',
        'status',
    ];

    public static function label(): string
    {
        return 'Estoques';
    }

    public static function singularLabel(): string
    {
        return 'Estoque';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Produto', 'product', Product::class)
                ->rules('required')
                ->help('Selecione o produto.'),

            BelongsTo::make('Armazém', 'warehouse', Warehouse::class)
                ->nullable()
                ->help('Selecione o armazém (opcional).'),

            BelongsTo::make('Empresa', 'company', Company::class)
                ->rules('required')
                ->help('Selecione a empresa.'),

            Select::make('Status', 'status')
                ->options([
                    'concluido' => 'Concluído',
                    'cancelado' => 'Cancelado',
                ])
                ->nullable()
                ->help('Selecione o status do estoque.'),

            Boolean::make('Disponível para Uso', 'is_available_use')
                ->default(true)
                ->help('Marque se está disponível para uso.'),

            Boolean::make('Vendido', 'is_sale')
                ->default(false)
                ->help('Marque se está vendido.'),

            Textarea::make('Histórico', 'history')
                ->nullable()
                ->hideFromIndex()
                ->help('Histórico em formato JSON (opcional).'),
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
        return "/resources/companies/{$resource->company_id}";
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource): string
    {
        return "/resources/companies/{$resource->company_id}";
    }
}