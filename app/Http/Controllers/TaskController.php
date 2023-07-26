<?php

namespace App\Http\Controllers;

use App\Jobs\ImportApp;
use App\Models\FileUpload;
use App\Services\JsonData;

class TaskController extends Controller
{
    public function start(FileUpload $file)
    {
        ImportApp::dispatch($file->id, new JsonData($file->path));

        return response()->json([
           'data' => [
               'message' => 'Start importing app'
           ]
        ]);
    }
}
