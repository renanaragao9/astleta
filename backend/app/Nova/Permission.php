<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Permission extends Resource
{
    public static $model = \App\Models\Permission::class;

    public static $title = 'label';

    public static $search = [
        'id',
        'name',
        'label',
    ];

    public static function label()
    {
        return 'Permissões';
    }

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Nome', 'name')->sortable(),
            Text::make('Descrição', 'label')->sortable(),
            Text::make('Grupo', 'group')->sortable(),
            BelongsToMany::make('Profile'),
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
        return 'Permissão';
    }

    public static function validateForCreation(NovaRequest $request): void
    {
        $request->validate([

            'name' => ['required'],
            'label' => ['required'],
            'group' => ['required'],
        ], [

            'required' => 'O campo é obrigatório',
        ]);

    }

    public static function validateForUpdate(NovaRequest $request, $resource = null): void
    {
        $request->validate([

            'name' => ['required'],
            'label' => ['required'],
            'group' => ['required'],
        ], [

            'required' => 'O campo é obrigatório',
        ]);
    }

    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return parent::redirectAfterCreate($request, $resource);
    }
}
