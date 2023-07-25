<?php

namespace App\Services;

use App\Models\FileUpload;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OldFiles
{
    /**
     * Get old files and delete them
     * @return void
     */
    public static function check()
    {
        $months = config('app.number_of_months_of_files_storage');
        $files = FileUpload::whereDate(
            'created_at', '<=', Carbon::now()->subMonths($months)
        )->get();

        foreach ($files as $file) {
            $file->delete();
            Log::info("Old file is automatically deleted $file->name | $file->path");
        }
    }

}
