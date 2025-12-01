<?php

namespace App\Providers;

use App\Services\CreateNodeRequest\Contracts\CreateNodeRequestInterface;
use App\Services\CreateNodeRequest\Services\CreateNodeRequest;
use App\Services\CustomerServices\Subscribe\Contracts\SubscribeManagerInterface;
use App\Services\Subscribe\Services\SubscribeManager;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->singleton(CreateNodeRequestInterface::class, CreateNodeRequest::class);
        $this->app->bind(SubscribeManagerInterface::class, SubscribeManager::class);
    }


    public function boot()
    {
        //
    }
}
