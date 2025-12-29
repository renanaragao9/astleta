<?php

namespace App\Services\Api\Athlete\Team;

use App\Helpers\UploadFIleToS3Helper;
use App\Models\Team;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UpdateTeamImageService extends BaseService
{
    public function __construct(
        private UploadFIleToS3Helper $uploadHelper
    ) {}

    public function run(Team $team, UploadedFile $image): array
    {
        $userId = $this->getUserId();

        if ($team->user_id !== $userId) {
            throw ValidationException::withMessages(['error' => 'Time não encontrado ou você não tem permissão.']);
        }

        if ($team->shield_path) {
            $this->deleteOldImage($team->shield_path);
        }

        $path = "teams/{$team->id}/shield";

        $uploadResult = $this->uploadHelper->run($image, $path);

        if (! $uploadResult || ! isset($uploadResult['key'])) {
            throw ValidationException::withMessages(['error' => 'Erro ao fazer upload da imagem.']);
        }

        $team->update([
            'shield_path' => $uploadResult['key'],
        ]);

        return [
            'shield_path' => $uploadResult['key'],
        ];
    }

    private function deleteOldImage(string $path): void
    {
        try {
            Storage::disk('s3')->delete($path);
        } catch (\Exception $e) {
            \Log::error('Erro ao deletar imagem antiga do time no S3.', [
                'path' => $path,
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
