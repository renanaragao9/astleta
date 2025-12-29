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

class UploadImageToFieldImageAction extends Action
{
    use InteractsWithQueue;
    use Queueable;

    public $name = 'Upload da Imagem do Campo';

    public function handle(ActionFields $fields, Collection $models): void
    {
        $fieldImage = $models[0];

        $originalName = $fields->file->getClientOriginalName();
        $uniqueName = time().'_'.uniqid().'_'.$originalName;

        $filepath = "fields/{$fieldImage->field->company->uuid}/{$fieldImage->field->uuid}/images/{$uniqueName}";

        $result = (new UploadFIleToS3Helper)->run($fields->file, $filepath);

        $filePath = $result['key'];

        $fieldImage->image_path = $filePath;
        $fieldImage->save();

        Action::message('Imagem enviada com sucesso');
    }

    public function fields(NovaRequest $request): array
    {
        return [
            File::make('Arquivo', 'file')
                ->rules('required', 'mimes:jpg,png,svg', 'max:4096'),
        ];
    }
}
