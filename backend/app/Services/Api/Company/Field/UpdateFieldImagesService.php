<?php

namespace App\Services\Api\Company\Field;

use App\Models\Field;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class UpdateFieldImagesService
{
    public function run(array $images, Field $field): Collection
    {
        $incomingIds = collect($images)
            ->filter(fn ($img) => ! empty($img['id']))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $field->fieldImages()
            ->whereNotIn('id', $incomingIds)
            ->get()
            ->each(function ($image) {
                if ($image->file) {
                    $urlParts = parse_url($image->file);
                    if (isset($urlParts['path'])) {
                        $path = ltrim($urlParts['path'], '/');
                        Storage::disk('s3')->delete($path);
                    }
                }

                $image->delete();
            });

        return collect($images)->map(function ($img) use ($field) {
            $existing = null;

            if (! empty($img['id'])) {
                $existing = $field->fieldImages()->where('id', $img['id'])->first();
            }

            $filePath = null;

            if (isset($img['file']) && $img['file'] instanceof UploadedFile && $img['file']->isValid()) {
                if ($existing && $existing->file) {
                    $urlParts = parse_url($existing->file);
                    if (isset($urlParts['path'])) {
                        $path = ltrim($urlParts['path'], '/');
                        Storage::disk('s3')->delete($path);
                    }
                }

                $storedPath = $img['file']->store('field_images', 's3');
                $filePath = Storage::url($storedPath);
            }

            if ($existing) {
                $existing->update([
                    'file' => $filePath ?? $existing->file,
                    'caption' => $img['caption'] ?? $existing->caption,
                ]);

                return $existing;
            }

            return $field->fieldImages()->create([
                'file' => $filePath,
                'caption' => $img['caption'] ?? null,
            ]);
        });
    }
}
