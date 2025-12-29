<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class GenerateTemporaryUrlSHelper
{
    public static function run(string $path, int $daysExpiration = 7): string
    {
        $timezone = config('app.timezone', 'America/Sao_Paulo');

        $expiration = Carbon::now($timezone)->addDays($daysExpiration);

        return Storage::disk('s3')->temporaryUrl(
            $path,
            $expiration
        );
    }
}
