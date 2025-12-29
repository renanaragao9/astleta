<?php

namespace App\Services\Api\Company\Field;

use App\Helpers\UploadFIleToS3Helper;
use App\Models\Field;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateFieldImageService
{
    public function __construct(
        private UploadFIleToS3Helper $uploadHelper
    ) {}

    public function run(Field $field, UploadedFile $image): array
    {
        try {
            if ($field->image_path) {
                $this->deleteOldImage($field->image_path);
            }

            $filepath = 'fields/images';

            $uploadResult = $this->uploadHelper->run($image, $filepath);

            if (! $uploadResult || ! isset($uploadResult['key'])) {
                return [
                    'status' => 'error',
                    'message' => 'Erro ao fazer upload da imagem.',
                    'data' => [],
                ];
            }

            $field->update([
                'image_path' => $uploadResult['key'],
            ]);

            return [
                'status' => 'success',
                'message' => 'Imagem atualizada com sucesso.',
                'data' => [
                    'image_path' => $uploadResult['key'],
                ],
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro interno do servidor ao atualizar imagem.',
                'data' => [],
            ];
        }
    }

    private function deleteOldImage(string $imagePath): void
    {
        try {
            Storage::disk('s3')->delete($imagePath);
        } catch (\Exception $e) {
            \Log::warning('Erro ao deletar imagem antiga: '.$e->getMessage());
        }
    }
}
