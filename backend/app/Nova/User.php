<?php

namespace App\Nova;

use App\Models\User as UserModel;
use App\Nova\Actions\UploadImageToUserAction;
use App\Nova\Filters\User\CreatedUserAtFromFilter;
use App\Nova\Filters\User\CreatedUserAtToFilter;
use App\Nova\Filters\User\UserBirthDateFromFilter;
use App\Nova\Filters\User\UserBirthDateToFilter;
use App\Nova\Filters\User\UserGenderFilter;
use App\Nova\Filters\User\UserIsActiveFilter;
use App\Nova\Filters\User\UserProfileFilter;
use Illuminate\Support\Str;
use Laravel\Nova\Auth\PasswordValidationRules;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class User extends Resource
{
    use PasswordValidationRules;

    public static $model = UserModel::class;

    public static $title = 'name';

    public static function label(): string
    {
        return 'Usuários';
    }

    public static function singularLabel(): string
    {
        return 'Usuário';
    }

    public static $search = [
        'id',
        'name',
        'email',
        'cpf',
        'uuid',
    ];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Hidden::make('UUID', 'uuid')
                ->default(fn() => (string) Str::uuid())
                ->rules('unique:users,uuid' . ($request->resourceId ? ',' . $request->resourceId . ',id' : ''))
                ->hideFromIndex(),

            Text::make('UUID', 'uuid')
                ->onlyOnDetail()
                ->copyable()
                ->help('Identificador único da empresa.'),

            Image::make('Imagem', 'image_path')
                ->path('users')
                ->disk('s3'),

            Text::make('Nome', 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Usuário', 'username')
                ->rules('required', 'max:254')
                ->creationRules('unique:users,username')
                ->updateRules("unique:users,username,{$request->resourceId},id")
                ->hideFromIndex(),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules("unique:users,email,{$request->resourceId},id"),

            Password::make('Senha', 'password')
                ->onlyOnForms()
                ->creationRules($this->passwordRules())
                ->updateRules($this->optionalPasswordRules()),

            BelongsTo::make('Perfil', 'profile', Profile::class)
                ->hideFromIndex()
                ->rules('required'),

            HasOne::make('Perfil de Atleta', 'athleteProfile', AthleteProfile::class),

            BelongsToMany::make('Habilidades', 'skills', Skill::class),

            Date::make('Data de Nascimento', 'date')
                ->rules('nullable', 'date'),

            Select::make('Gênero', 'gender')
                ->options([
                    'masculino' => 'Masculino',
                    'feminino' => 'Feminino',
                    'outro' => 'Outro',
                    'nao_informado' => 'Prefiro não informar',
                ])
                ->displayUsingLabels(),

            Text::make('CPF', attribute: 'cpf')
                ->rules('nullable', 'max:14')
                ->creationRules('unique:users,cpf')
                ->updateRules("unique:users,cpf,{$request->resourceId},id"),

            Text::make('Telefone', 'phone')
                ->rules('required', 'max:20')
                ->creationRules('unique:users,phone')
                ->updateRules("unique:users,phone,{$request->resourceId},id"),

            Number::make('Quantidade de Logins', 'qtd_login')
                ->onlyOnDetail()
                ->readonly(),

            DateTime::make('Último Login', 'last_login')
                ->onlyOnDetail()
                ->readonly(),

            Select::make('Tipo', 'type')
                ->options([
                    'normal' => 'Normal',
                    'test' => 'Teste',
                    'demo' => 'Demo',
                ])
                ->onlyOnDetail()
                ->default('normal'),

            Select::make('Idioma', 'lang')
                ->options([
                    'pt' => 'Português',
                    'en' => 'Inglês',
                ])
                ->onlyOnDetail()
                ->default('pt'),

            DateTime::make('Email Verificado Em', 'email_verified_at'),

            DateTime::make('Telefone Verificado Em', 'phone_verified_at')
                ->onlyOnDetail()
                ->readonly(),

            Text::make('Provedor', 'provider')
                ->onlyOnDetail()
                ->readonly(),

            Text::make('ID do Provedor', 'provider_id')
                ->onlyOnDetail()
                ->readonly(),

            Text::make('ID do Cliente Asaas', 'asaas_customer_id')
                ->onlyOnDetail()
                ->readonly(),

            Boolean::make('Ativo', 'is_active')
                ->default(true),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new CreatedUserAtFromFilter,
            new CreatedUserAtToFilter,
            new UserIsActiveFilter,
            new UserProfileFilter,
            new UserGenderFilter,
            new UserBirthDateFromFilter,
            new UserBirthDateToFilter,
        ];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new UploadImageToUserAction,
        ];
    }
}
