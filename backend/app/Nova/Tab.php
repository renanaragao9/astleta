<?php

namespace App\Nova;

use App\Models\Tab as TabModel;
use App\Nova\Actions\ToggleTabStatusAction;
use App\Nova\Filters\Tab\TabOpenedAtFromFilter;
use App\Nova\Filters\Tab\TabOpenedAtToFilter;
use App\Nova\Filters\Tab\TabPaymentFormFilter;
use App\Nova\Filters\Tab\TabStatusFilter;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Tab extends Resource
{
    public static $model = TabModel::class;

    public static $title = 'code';

    public static $search = [
        'id',
        'code',
        'customer_name',
    ];

    public static function label(): string
    {
        return 'Comandas';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Código', 'code')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Cliente', 'customer_name')
                ->sortable()
                ->rules('required', 'max:255'),

            Select::make('Status', 'status')
                ->options([
                    'aberto' => 'Aberto',
                    'pago' => 'Pago',
                    'cancelado' => 'Cancelado',
                ])
                ->displayUsingLabels()
                ->sortable()
                ->rules('required'),

            Currency::make('Valor Total', 'total_amount')
                ->currency('BRL')
                ->sortable()
                ->rules('nullable', 'numeric', 'min:0')
                ->step(0.01),

            DateTime::make('Aberto em', 'opened_at')
                ->sortable(),

            DateTime::make('Fechado em', 'closed_at')
                ->sortable(),

            BelongsTo::make('Forma de Pagamento', 'paymentForm', PaymentForm::class)
                ->nullable()
                ->sortable(),

            BelongsTo::make('Empresa', 'company', Company::class)
                ->sortable(),

            HasMany::make('Itens da Comanda', 'tabItems', TabItem::class),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new TabOpenedAtFromFilter,
            new TabOpenedAtToFilter,
            new TabStatusFilter,
            new TabPaymentFormFilter,
        ];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new ToggleTabStatusAction,
        ];
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
