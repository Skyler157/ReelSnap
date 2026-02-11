<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\InstagramDownloaderInterface;
use App\Services\RapidApiDownloader;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            InstagramDownloaderInterface::class,
            RapidApiDownloader::class
        );
    }

    public function boot(): void
    {
        //
    }
}