<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait CleansTemporaryFiles
{
    /**
     * Remove um arquivo temporário
     */
    protected function cleanTemporaryFile(string $filePath): void
    {
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }

    /**
     * Remove múltiplos arquivos temporários
     */
    protected function cleanTemporaryFiles(array $filePaths): void
    {
        foreach ($filePaths as $filePath) {
            $this->cleanTemporaryFile($filePath);
        }
    }
}
