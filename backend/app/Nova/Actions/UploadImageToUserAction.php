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

class UploadImageToUserAction extends Action
{
    use InteractsWithQueue;
    use Queueable;

    public $name = 'Upload da Imagem do Usuário';

    public function handle(ActionFields $fields, Collection $models): void
    {
        $user = $models[0];

        $originalName = $fields->file->getClientOriginalName();
        $uniqueName = time().'_'.uniqid().'_'.$originalName;

        $filepath = "users/{$user->uuid}/{$uniqueName}";

        $result = (new UploadFIleToS3Helper)->run($fields->file, $filepath);

        $filePath = $result['key'];

        $user->image_path = $filePath;
        $user->save();

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
