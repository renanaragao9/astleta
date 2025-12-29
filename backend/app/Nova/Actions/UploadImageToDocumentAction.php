<?php

namespace App\Nova\Actions;

use App\Helpers\UploadFIleToS3Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Http\Requests\NovaRequest;

class UploadImageToDocumentAction extends Action
{
    use InteractsWithQueue;
    use Queueable;

    public $name = 'Upload da Imagem do Documento';

    public function handle(ActionFields $fields, Collection $models): void
    {
        $document = $models[0];

        $document->load('documentable');

        $originalName = $fields->file->getClientOriginalName();
        $uniqueName = time().'_'.uniqid().'_'.$originalName;

        $filepath = "documents/{$document->documentable->uuid}/{$document->id}/{$uniqueName}";

        $result = (new UploadFIleToS3Helper)->run($fields->file, $filepath);

        $filePath = $result['key'];

        $document->file_path = $filePath;
        $document->save();

        Action::message('Imagem enviada com sucesso');
    }

    public function fields(NovaRequest $request): array
    {
        return [
            File::make('Arquivo', 'file')
                ->rules('required', 'max:4096'),
        ];
    }
}
