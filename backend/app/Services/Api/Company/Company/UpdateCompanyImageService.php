<?php

namespace App\Services\Api\Company\Company;

use App\Helpers\UploadFIleToS3Helper;
use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UpdateCompanyImageService
{
    public function __construct(
        private UploadFIleToS3Helper $uploadHelper
    ) {}

    public function run(UploadedFile $image): array
    {
        $userId = Auth::id();
        $company = Company::where('user_id', $userId)->first();

        if (! $company) {
            return [
                'status' => 'error',
                'message' => 'Empresa não encontrada.',
                'data' => [],
            ];
        }

        try {
            if ($company->image_path) {
                $this->deleteOldImage($company->image_path);
            }

            $filepath = 'companies/images';

            $uploadResult = $this->uploadHelper->run($image, $filepath);

            if (! $uploadResult || ! isset($uploadResult['key'])) {
                return [
                    'status' => 'error',
                    'message' => 'Erro ao fazer upload da imagem.',
                    'data' => [],
                ];
            }

            $company->update([
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
