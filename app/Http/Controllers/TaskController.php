<?php

namespace App\Http\Controllers;

use App\Jobs\ImportApp;
use App\Models\FileUpload;

class TaskController extends Controller
{
    public function start(FileUpload $file)
    {
        ImportApp::dispatch($file);

        return response()->json([
           'data' => [
               'message' => 'Start importing app'
           ]
        ]);
    }
}
