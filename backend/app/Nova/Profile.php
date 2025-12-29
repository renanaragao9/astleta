<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Profile extends Resource
{
    public static $model = \App\Models\Profile::class;

    public static $title = 'name';

    public static function label()
    {
        return 'Perfis';
    }

    public static $search = [
        'id',
        'name',
    ];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            Text::make('Nome', 'name')->sortable(),
            BelongsToMany::make('Permissão', 'Permission', Permission::class)
                ->singularLabel('Permissão'),
        ];
    }

    public function cards(NovaRequest $request)
    {
        return [];
    }

    public function filters(NovaRequest $request)
    {
        return [];
    }

    public function lenses(NovaRequest $request)
    {
        return [];
    }

    public function actions(NovaRequest $request)
    {
        return [];
    }

    public static function singularLabel()
    {
        return 'Perfil';
    }

    public static function validateForCreation(NovaRequest $request): void
    {
        $request->validate([

            'name' => ['required'],
        ], [

            'required' => 'O campo é obrigatório',
        ]);

    }

    public static function validateForUpdate(NovaRequest $request, $resource = null): void
    {
        $request->validate([

            'name' => ['required'],
        ], [

            'required' => 'O campo é obrigatório',
        ]);
    }

    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return parent::redirectAfterCreate($request, $resource);
    }
}
