<?php

use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'file'], function() {
    Route::post('/store', [FileUploadController::class, 'store']);
    Route::get('/list', [FileUploadController::class, 'index']);
    Route::get('/show/{file}', [FileUploadController::class, 'show']);
    Route::delete('/delete/{file}', [FileUploadController::class, 'destroy']);
});

Route::group(['prefix' => 'task'], function () {
    Route::get('/start/{file}', [TaskController::class, 'start']);
});
