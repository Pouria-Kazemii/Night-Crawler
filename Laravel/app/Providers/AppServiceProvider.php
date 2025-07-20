<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\CrawlerManagerInterface;
use App\Services\CrawlerManager;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->singleton(CrawlerManagerInterface::class, CrawlerManager::class);
    }


    public function boot()
    {
        //
    }
}
