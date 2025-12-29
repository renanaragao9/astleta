<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class ToggleExpenseTypeAction extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Alterar Tipo';

    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Tipo', 'type')
                ->options([
                    'entrada' => 'Entrada',
                    'saida' => 'Saída',
                ])
                ->rules('required'),
        ];
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        $type = $fields->type;

        $models->each(function ($model) use ($type) {
            $model->update(['type' => $type]);
        });

        $typeLabel = $type === 'entrada' ? 'Entrada' : 'Saída';

        return Action::message("Tipo alterado para {$typeLabel} com sucesso.");
    }
}
