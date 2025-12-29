<?php

namespace App\Nova;

use App\Models\Contact as ContactModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Contact extends Resource
{
    public static $model = ContactModel::class;

    public static $title = 'value';

    public static $search = [
        'id',
        'value',
    ];

    public static function label(): string
    {
        return 'Contatos';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Tipo de Contato', 'contactType', ContactType::class)
                ->sortable()
                ->help('Tipo do contato, como telefone, email, etc.'),

            Text::make('Valor', 'value')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Valor do contato, como o número de telefone ou endereço de email.'),

            MorphTo::make('Relacionado', 'contactable')->types([
                User::class,
                Company::class,
            ])->sortable()
                ->help('Entidade relacionada ao contato, como um usuário.'),
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
