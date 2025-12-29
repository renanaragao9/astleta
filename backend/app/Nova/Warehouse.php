<?php

namespace App\Nova;

use App\Models\Warehouse as WarehouseModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Warehouse extends Resource
{
    public static $model = WarehouseModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
        'location',
    ];

    public static function label(): string
    {
        return 'Armazéns';
    }

    public static function singularLabel(): string
    {
        return 'Armazém';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Nome', 'name')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Digite o nome do armazém.'),

            Text::make('Localização', 'location')
                ->nullable()
                ->hideFromIndex()
                ->help('Digite a localização do armazém (opcional).'),

            BelongsTo::make('Empresa', 'company', Company::class)
                ->rules('required')
                ->help('Escolha a empresa responsável por este armazém.'),

            HasMany::make('Estoques', 'stocks', Stock::class),
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