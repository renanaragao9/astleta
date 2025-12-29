<?php

namespace App\Nova;

use App\Models\Expense as ExpenseModel;
use App\Nova\Actions\ToggleExpenseTypeAction;
use App\Nova\Actions\TogglePaidStatusAction;
use App\Nova\Filters\Expense\ExpenseCreatedAtFromFilter;
use App\Nova\Filters\Expense\ExpenseCreatedAtToFilter;
use App\Nova\Filters\Expense\ExpenseDueDateFromFilter;
use App\Nova\Filters\Expense\ExpenseDueDateToFilter;
use App\Nova\Filters\Expense\ExpenseTypeFilter;
use App\Nova\Filters\Expense\ExpenseTypeValueFilter;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Expense extends Resource
{
    public static $model = ExpenseModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
        'description',
    ];

    public static function label(): string
    {
        return 'Despesas';
    }

    public static function singularLabel(): string
    {
        return 'Despesa';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Nome', 'name')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Digite um nome descritivo para identificar esta despesa.'),

            Select::make('Tipo', 'type')
                ->options([
                    'entrada' => 'Entrada',
                    'saida' => 'Saída',
                ])
                ->displayUsingLabels()
                ->rules('required')
                ->help('Selecione se esta é uma entrada (receita) ou saída (despesa) de dinheiro.')
                ->sortable(),

            Currency::make('Valor', 'amount')
                ->currency('BRL')
                ->rules('required', 'numeric', 'min:0')
                ->help('Digite o preço por hora do campo. Separe as casas decimais por ponto. Exemplo: 120.50')
                ->sortable(),

            BelongsTo::make('Tipo de Despesa', 'expenseType', ExpenseType::class)
                ->rules('required')
                ->help('Selecione a categoria específica desta despesa.')
                ->sortable(),

            Date::make('Data de Vencimento', 'due_date')
                ->nullable()
                ->help('Selecione a data de vencimento desta despesa (opcional).')
                ->sortable(),

            Textarea::make('Descrição', 'description')
                ->nullable()
                ->hideFromIndex()
                ->help('Adicione detalhes adicionais sobre esta despesa (opcional).'),

            BelongsTo::make('Empresa', 'company', Company::class)
                ->rules('required')
                ->help('Escolha a empresa à qual esta despesa está relacionada.'),

            Badge::make('Status', fn () => $this->is_paid ? 'pago' : 'pendente')
                ->map([
                    'pendente' => 'warning',
                    'pago' => 'success',
                ])
                ->sortable()
                ->exceptOnForms(),

            Boolean::make('Pago', 'is_paid')
                ->default(false)
                ->help('Marque se esta despesa já foi paga ou liquidada.')
                ->sortable()
                ->onlyOnForms(),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new ExpenseCreatedAtFromFilter,
            new ExpenseCreatedAtToFilter,
            new ExpenseDueDateFromFilter,
            new ExpenseDueDateToFilter,
            new ExpenseTypeFilter,
            new ExpenseTypeValueFilter,
        ];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new TogglePaidStatusAction,
            new ToggleExpenseTypeAction,
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
