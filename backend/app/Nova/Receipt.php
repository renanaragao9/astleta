<?php

namespace App\Nova;

use App\Models\Receipt as ReceiptModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Receipt extends Resource
{
    public static $model = ReceiptModel::class;

    public static $title = 'number';

    public static $search = [
        'id',
        'number',
    ];

    public static function label(): string
    {
        return 'Recibos';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),

            File::make('Arquivo', 'file_path')
                ->path('receipts/files')
                ->disk('s3')
                ->nullable()
                ->hideFromIndex(),

            Text::make('Número', 'number')
                ->sortable()
                ->rules('required')
                ->creationRules('unique:receipts,number')
                ->updateRules('unique:receipts,number,{{resourceId}}'),

            Select::make('Status', 'status')
                ->options([
                    'pendente' => 'Pendente',
                    'pago' => 'Pago',
                    'cancelado' => 'Cancelado',
                ])
                ->default('pendente'),

            Textarea::make('Descrição', 'description')
                ->nullable(),

            Date::make('Emitido em', 'issued_at')
                ->rules('required'),

            Date::make('Data de Pagamento', 'paymente_date')
                ->nullable(),

            Number::make('Valor', 'amount')
                ->rules('required', 'numeric', 'min:0'),

            Hidden::make('ID do Pagamento Asaas', 'asaas_payment_id')
                ->nullable(),

            Hidden::make('ID do Cliente Asaas', 'asaas_customer_id')
                ->nullable(),

            BelongsTo::make('Empresa', 'company', Company::class)
                ->sortable(),

            BelongsTo::make('Usuário', 'user', User::class)
                ->sortable()
                ->default(function ($request) {
                    return $request->user()->id ?? null;
                }),
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
