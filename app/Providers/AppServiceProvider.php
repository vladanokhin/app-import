<?php

namespace App\Providers;

use App\Models\FileUpload;
use App\Observers\FileUploadObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FileUpload::observe(FileUploadObserver::class);
    }
}
