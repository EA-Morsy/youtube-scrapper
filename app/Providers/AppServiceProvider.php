<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\PlaylistScrapingService::class);
        $this->app->singleton(\App\Services\YouTubeService::class);
        $this->app->singleton(\App\Services\AIService::class);
    }

    /**
     * Bootstrap any application services.
     */

    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
