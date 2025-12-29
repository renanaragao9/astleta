<?php

namespace App\Nova;

use App\Models\Supplier as SupplierModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\HasMany;

class Supplier extends Resource
{
    public static $model = SupplierModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
        'email',
        'phone',
    ];

    public static function label(): string
    {
        return 'Fornecedores';
    }

    public static function singularLabel(): string
    {
        return 'Fornecedor';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Nome', 'name')
                ->rules('required', 'max:255')
                ->help('Digite o nome do fornecedor.')
                ->sortable(),

            Text::make('E-mail', 'email')
                ->nullable()
                ->rules('nullable', 'email', 'unique:suppliers,email' . ($request->resourceId ? ',' . $request->resourceId . ',id' : ''), 'max:255')
                ->help('Digite o e-mail do fornecedor (opcional).'),

            Text::make('Telefone', 'phone')
                ->nullable()
                ->rules('nullable', 'unique:suppliers,phone' . ($request->resourceId ? ',' . $request->resourceId . ',id' : ''), 'max:20')
                ->help('Digite o telefone do fornecedor (opcional).'),

            Text::make('Endereço', 'address')
                ->nullable()
                ->hideFromIndex()
                ->help('Digite o endereço do fornecedor (opcional).'),

            BelongsTo::make('Empresa', 'company', Company::class)
                ->rules('required')
                ->help('Selecione a empresa.'),

            HasMany::make('Compras', 'purchases', Purchase::class),
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