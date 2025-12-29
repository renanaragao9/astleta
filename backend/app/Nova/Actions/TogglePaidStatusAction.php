<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class TogglePaidStatusAction extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Marcar/Desmarcar como Pago';

    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Ação', 'action')
                ->options([
                    'mark_paid' => 'Marcar como Pago',
                    'mark_unpaid' => 'Marcar como Não Pago',
                ])
                ->rules('required'),
        ];
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        $action = $fields->action;

        if ($action === 'mark_paid') {
            $models->each(function ($model) {
                $model->update(['is_paid' => true]);
            });

            return Action::message('Despesas marcadas como pagas com sucesso.');

        } elseif ($action === 'mark_unpaid') {
            $models->each(function ($model) {
                $model->update(['is_paid' => false]);
            });

            return Action::message('Despesas marcadas como não pagas com sucesso.');
        }

        return Action::danger('Ação inválida.');
    }
}
