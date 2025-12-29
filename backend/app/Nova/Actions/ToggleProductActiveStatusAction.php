<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class ToggleProductActiveStatusAction extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Marcar/Desmarcar como Ativo';

    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Status', 'status')
                ->options([
                    'active' => 'Marcar como Ativo',
                    'inactive' => 'Marcar como Inativo',
                ])
                ->rules('required'),
        ];
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        $status = $fields->status;

        if ($status === 'active') {
            $models->each(function ($model) {
                $model->update(['is_active' => true]);
            });

            return Action::message('Produtos marcados como ativos com sucesso.');

        } elseif ($status === 'inactive') {
            $models->each(function ($model) {
                $model->update(['is_active' => false]);
            });

            return Action::message('Produtos marcados como inativos com sucesso.');
        }

        return Action::danger('Status inválido.');
    }
}
