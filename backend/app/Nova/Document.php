<?php

namespace App\Nova;

use App\Helpers\GenerateTemporaryUrlSHelper;
use App\Models\Document as DocumentModel;
use App\Nova\Actions\ToggleDocumentStatusAction;
use App\Nova\Actions\UploadImageToDocumentAction;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Document extends Resource
{
    public static $model = DocumentModel::class;

    public static $title = 'number';

    public static $search = [
        'id',
        'number',
    ];

    public static function label(): string
    {
        return 'Documentos';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Imagem', 'file_path', function ($value) {
                if (! $value) {
                    return null;
                }
                $fileLink = (new GenerateTemporaryUrlSHelper)->run($value);

                return "<a href=\"$fileLink\" target='_blank' download><img src=\"$fileLink\" style=\"max-width: 400px;\"></a>";
            })->asHtml()
                ->withMeta([
                    'extraAttributes' => [
                        'readonly' => true,
                    ],
                ])
                ->onlyOnDetail(),

            BelongsTo::make('Tipo de Documento', 'documentType', DocumentType::class)
                ->sortable()
                ->help('Tipo ou categoria do documento.'),

            Text::make('Número', 'number')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Número de identificação do documento.'),

            Select::make('Status', 'status')
                ->options([
                    'pendente' => 'Pendente',
                    'aprovado' => 'Aprovado',
                    'rejeitado' => 'Rejeitado',
                ])
                ->displayUsingLabels()
                ->default('pendente')
                ->rules('required')
                ->help('Status atual do documento (pendente, aprovado ou rejeitado).'),

            Textarea::make('Descrição', 'description')
                ->nullable()
                ->rows(3)
                ->help('Descrição opcional do documento.'),

            MorphTo::make('Relacionado', 'documentable')->types([
                User::class,
                Company::class,
            ])->sortable()
                ->help('Entidade relacionada ao documento, como um usuário.'),

            Badge::make('Status')
                ->map([
                    'pendente' => 'warning',
                    'aprovado' => 'success',
                    'rejeitado' => 'danger',
                ]),
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
        return [
            new ToggleDocumentStatusAction,
            new UploadImageToDocumentAction,
        ];
    }
}
