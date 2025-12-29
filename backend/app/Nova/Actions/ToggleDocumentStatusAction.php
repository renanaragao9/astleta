<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class ToggleDocumentStatusAction extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Alterar Status';

    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Status', 'status')
                ->options([
                    'pendente' => 'Pendente',
                    'aprovado' => 'Aprovado',
                    'rejeitado' => 'Rejeitado',
                ])
                ->rules('required'),
        ];
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        $status = $fields->status;

        $models->each(function ($model) use ($status) {
            $model->update(['status' => $status]);
        });

        $statusLabel = match ($status) {
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'rejeitado' => 'Rejeitado',
        };

        return Action::message("Status alterado para '{$statusLabel}' com sucesso.");
    }
}
