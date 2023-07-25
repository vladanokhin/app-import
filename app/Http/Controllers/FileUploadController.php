<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadStoreRequest;
use App\Http\Resources\FileUploadResource;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FileUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return FileUploadResource::collection(FileUpload::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FileUploadStoreRequest $request)
    {
        $file = $request->file('file');

        $fileUpload = FileUpload::create([
            'name' => $file->getClientOriginalName(),
            'path' => $file->store('files'),
        ]);
        Log::info("User upload file $fileUpload->name | $fileUpload->path");

        return FileUploadResource::make($fileUpload);
    }

    /**
     * Display the specified resource.
     */
    public function show(FileUpload $file)
    {
        return FileUploadResource::make($file);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FileUpload $file)
    {
        $file->delete();
        Log::info("User deleted file $file->name | $file->path");

        return FileUploadResource::make($file);
    }
}
