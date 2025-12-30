<?php

namespace App\Helpers;

use Aws\S3\S3Client;

class UploadFIleToS3Helper
{
    public function run($file, $path)
    {
        $content = file_get_contents($file->getRealPath());
        $hash = hash('sha256', $content . time());
        $extension = $file->getClientOriginalExtension();
        $filename = $hash . '.' . $extension;
        $key = "seuracha/{$path}/{$filename}";
        $mimetype = mime_content_type($file->getRealPath());

        $s3 = new S3Client([
            'region' => 'sa-east-1',
        ]);

        $returnedData = $s3->putObject([
            'Bucket' => env('AWS_BUCKET'),
            'Key' => $key,
            'Body' => $content,
            'ContentType' => $mimetype,
        ]);

        $returnedData['key'] = $key;

        return $returnedData;
    }
}
