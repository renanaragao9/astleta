<?php

namespace App\Services\Api\Company\Field;

use App\Models\FieldImage;
use Illuminate\Support\Collection;

class CreateFieldImagesService
{
    public function run(array $images, int $fieldId): Collection
    {
        return collect($images)->map(function ($image) use ($fieldId) {
            $file = $image['file'];

            $path = $file->store(env('AWS_BUCKET', 'seuracha') . '/fields/images', 's3');

            return FieldImage::create([
                'field_id' => $fieldId,
                'file' => $path,
                'caption' => $image['caption'] ?? null,
            ]);
        });
    }
}
