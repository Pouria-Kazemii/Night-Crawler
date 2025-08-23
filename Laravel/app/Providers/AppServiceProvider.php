<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\CreateNodeRequestInterface;
use App\Services\CreateNodeRequest;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->singleton(CreateNodeRequestInterface::class, CreateNodeRequest::class);
    }


    public function boot()
    {
        //
    }
}
