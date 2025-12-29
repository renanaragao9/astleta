<?php

namespace App\Nova;

use App\Models\Company as CompanyModel;
use App\Nova\Actions\UploadImageToCompanyAction;
use App\Nova\Filters\Company\CompanyIsFreeFilter;
use App\Nova\Filters\Company\CompanyIsOpenFilter;
use App\Nova\Filters\Company\CompanyStatusFilter;
use App\Nova\Filters\Company\CompanyTypeFilter;
use App\Nova\Filters\Company\CreatedCompanyAtFromFilter;
use App\Nova\Filters\Company\CreatedCompanyAtToFilter;
use Customfields\Cnpj\Cnpj;
use Customfields\Cpf\Cpf;
use Customfields\PhoneNumber\PhoneNumber;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Company extends Resource
{
    public static $model = CompanyModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
        'cnpj',
        'cpf',
        'phone',
        'user.name',
    ];

    public static function label(): string
    {
        return 'Empresas';
    }

    public static function singularLabel(): string
    {
        return 'Empresa';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Hidden::make('UUID', 'uuid')
                ->default(fn() => Str::uuid())
                ->hideFromIndex(),

            Text::make('UUID', 'uuid')
                ->onlyOnDetail()
                ->copyable()
                ->help('Identificador único da empresa.'),

            Image::make('Imagem', 'image_path')
                ->path('companies/images')
                ->disk('s3'),

            Text::make('Nome', 'name')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Digite o nome da empresa.'),

            Select::make('Status', 'status')
                ->options([
                    'pendente' => 'Pendente',
                    'aprovado' => 'Aprovado',
                    'rejeitado' => 'Rejeitado',
                    'inadimplente' => 'Inadimplente',
                ])
                ->displayUsingLabels()
                ->rules('required')
                ->help('Selecione o status da empresa.'),

            Select::make('Tipo', 'type')
                ->options([
                    'pj' => 'Pessoa Jurídica',
                    'fisica' => 'Pessoa Física',
                ])
                ->displayUsingLabels()
                ->rules('required')
                ->help('Selecione o tipo de pessoa.'),

            Cnpj::make('CNPJ', 'cnpj')
                ->rules('nullable', 'required_if:type,pj', 'unique:companies,cnpj' . ($request->resourceId ? ',' . $request->resourceId . ',id' : ''), 'max:18')
                ->hideFromIndex()
                ->help('Digite o CNPJ da empresa (apenas para PJ).'),

            Cpf::make('CPF', 'cpf')
                ->rules('nullable', 'required_if:type,fisica', 'unique:companies,cpf' . ($request->resourceId ? ',' . $request->resourceId . ',id' : ''), 'max:14')
                ->hideFromIndex()
                ->help('Digite o CPF (apenas para pessoa física).'),

            PhoneNumber::make('Telefone', 'phone')
                ->rules('required', 'unique:companies,phone' . ($request->resourceId ? ',' . $request->resourceId . ',id' : ''), 'max:20')
                ->help('Digite o número de telefone.'),

            Textarea::make('Descrição', 'description')
                ->nullable()
                ->hideFromIndex()
                ->help('Descrição opcional da empresa.'),

            Boolean::make('Está Aberto', 'is_open')
                ->help('Marque se a empresa está aberta.'),

            Boolean::make('É Gratuito', 'is_free')
                ->help('Marque se a empresa é gratuita.'),

            BelongsTo::make('Usuário', 'user', User::class)
                ->searchable()
                ->rules('required')
                ->help('Selecione o usuário associado.'),

            HasMany::make('Reservas', 'bookingsThroughFields', Booking::class),
            HasMany::make('Comandas', 'tabs', Tab::class),
            HasMany::make('Campos', 'fields', Field::class),
            HasMany::make('Despesas', 'expenses', Expense::class),
            HasMany::make('Produtos', 'products', Product::class),
            HasMany::make('Cupons', 'coupons', Coupon::class),
            HasMany::make('Armazéns', 'warehouses', Warehouse::class),
            HasMany::make('Estoques', 'stocks', Stock::class),
            HasMany::make('Fornecedores', 'suppliers', Supplier::class),
            HasMany::make('Compras', 'purchases', Purchase::class),
            HasMany::make('Torneios', 'tournaments', Tournament::class),

            MorphMany::make('Endereços', 'addresses', Address::class),
            MorphMany::make('Contatos', 'contacts', Contact::class),
            MorphMany::make('Documentos', 'documents', Document::class),
            MorphToMany::make('Tags', 'tags', Tag::class),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new CreatedCompanyAtFromFilter,
            new CreatedCompanyAtToFilter,
            new CompanyStatusFilter,
            new CompanyIsOpenFilter,
            new CompanyTypeFilter,
            new CompanyIsFreeFilter,
        ];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new UploadImageToCompanyAction,
        ];
    }
}
