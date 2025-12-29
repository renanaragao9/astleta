<?php

namespace App\Nova;

use App\Models\PaymentForm as PaymentFormModel;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class PaymentForm extends Resource
{
    public static $model = PaymentFormModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
    ];

    public static function label(): string
    {
        return 'Formas de Pagamento';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Nome', 'name')->sortable(),
            Select::make('Tipo', 'type')->options([
                'online' => 'Online',
                'presencial' => 'Presencial',
            ])->sortable(),
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
