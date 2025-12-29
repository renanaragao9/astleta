<?php

namespace App\Services\Api\Athlete\Profile;

use App\Helpers\UploadFIleToS3Helper;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateUserImageService
{
    public function __construct(
        private UploadFIleToS3Helper $uploadHelper
    ) {}

    public function run(User $user, UploadedFile $image): array
    {
        try {
            if ($user->image_path) {
                $this->deleteOldImage($user->image_path);
            }

            $path = 'users/'.$user->uuid.'/profile';

            $uploadResult = $this->uploadHelper->run($image, $path);

            if (! $uploadResult || ! isset($uploadResult['key'])) {
                return [
                    'status' => 'error',
                    'message' => 'Erro ao fazer upload da imagem.',
                    'data' => [],
                ];
            }

            $user->update([
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
