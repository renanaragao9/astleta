<?php

namespace App\Nova;

use App\Models\Purchase as PurchaseModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Purchase extends Resource
{
    public static $model = PurchaseModel::class;

    public static $title = 'invoice_number';

    public static $search = [
        'id',
        'invoice_number',
        'status',
    ];

    public static function label(): string
    {
        return 'Compras';
    }

    public static function singularLabel(): string
    {
        return 'Compra';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Número da Nota Fiscal', 'invoice_number')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Digite o número da nota fiscal.'),

            Select::make('Status', 'status')
                ->options([
                    'pendente' => 'Pendente',
                    'aprovada' => 'Aprovada',
                    'cancelada' => 'Cancelada',
                    'concluida' => 'Concluída',
                ])
                ->default('pendente')
                ->help('Selecione o status da compra.'),

            Date::make('Data da Compra', 'purchase_date')
                ->rules('required')
                ->help('Selecione a data da compra.'),

            Currency::make('Valor Total', 'total_amount')
                ->currency('BRL')
                ->rules('required', 'numeric', 'min:0')
                ->help('Digite o valor total da compra.'),

            BelongsTo::make('Fornecedor', 'supplier', Supplier::class)
                ->rules('required')
                ->help('Selecione o fornecedor.'),

            BelongsTo::make('Empresa', 'company', Company::class)
                ->rules('required')
                ->help('Selecione a empresa.'),
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