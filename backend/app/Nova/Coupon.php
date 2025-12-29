<?php

namespace App\Nova;

use App\Models\Coupon as CouponModel;
use App\Nova\Actions\ToggleCouponActiveStatusAction;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Coupon extends Resource
{
    public static $model = CouponModel::class;

    public static $title = 'code';

    public static $search = [
        'id',
        'code',
    ];

    public static function label(): string
    {
        return 'Cupons';
    }

    public static function singularLabel(): string
    {
        return 'Cupom';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Código', 'code')
                ->sortable()
                ->rules(
                    'required',
                    'max:255',
                )
                ->help('Código único do cupom'),

            Select::make('Tipo de Desconto', 'discount_type')
                ->options([
                    'percentage' => 'Percentual',
                    'amount' => 'Valor Fixo',
                ])
                ->rules('required')
                ->help('Tipo do desconto aplicado'),

            Number::make('Valor do Desconto', 'discount_amount')
                ->min(0)
                ->step(0.01)
                ->rules('required', 'numeric', 'min:0')
                ->help('Valor fixo do desconto (se aplicável)'),

            Number::make('Percentual de Desconto', 'discount_percentage')
                ->min(0)
                ->max(100)
                ->step(0.01)
                ->rules('required', 'numeric', 'min:0', 'max:100')
                ->help('Percentual de desconto (0-100%)')
                ->sortable(),

            Number::make('Limite de Uso', 'usage_limit')
                ->min(0)
                ->rules('required', 'integer', 'min:0')
                ->help('Número máximo de vezes que o cupom pode ser utilizado'),

            Date::make('Data de Início', 'start_date')
                ->rules('required', 'date')
                ->help('Data a partir da qual o cupom pode ser utilizado')
                ->sortable(),

            Date::make('Data de Fim', 'end_date')
                ->rules('required', 'date', 'after:start_date')
                ->help('Data limite para utilização do cupom'),

            DateTime::make('Data de Expiração', 'expires_at')
                ->nullable()
                ->help('Data e hora de expiração do cupom'),

            Boolean::make('Ativo', 'is_active')
                ->default(true)
                ->help('Se o cupom está ativo'),

            BelongsTo::make('Empresa', 'company', Company::class)
                ->rules('required')
                ->help('Empresa responsável pelo cupom'),
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
        return [
            new ToggleCouponActiveStatusAction,
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
