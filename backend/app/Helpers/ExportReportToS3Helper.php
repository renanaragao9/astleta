<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Facades\Excel;

class ExportReportToS3Helper
{
    public static function run($exportClass, $filename)
    {

        $exported = Excel::raw($exportClass, ExcelType::XLSX);
        Storage::disk('s3')->put($filename, $exported, 'private');

        return GenerateTemporaryUrlSHelper::run($filename);
    }
}
