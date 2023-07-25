<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use App\Services\JsonData;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function start(FileUpload $file)
    {
        $jsonData = new JsonData($file->path);
    }
}
