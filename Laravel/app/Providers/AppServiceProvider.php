<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Elastic\Elasticsearch\Client;
use Elastic\Transport\Transport;
use Elastic\Transport\TransportInterface;
use Elastic\Transport\NodePool\StaticNodePool;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
      
    }

    public function boot()
    {
        //
    }
}
