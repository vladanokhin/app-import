<?php

namespace App\Observers;

use App\Models\FileUpload;
use Illuminate\Support\Facades\Storage;

class FileUploadObserver
{
    /**
     * Handle the FileUpload "deleted" event.
     * @param FileUpload $file
     * @return void
     */
    public function deleted(FileUpload $file): void
    {
        Storage::delete($file->path);
    }
}
