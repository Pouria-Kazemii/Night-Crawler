<?php

use App\Http\Controllers\Api\CustomerServiceController;
use App\Http\Middleware\CustomerMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1/services')->controller(CustomerServiceController::class)->middleware(CustomerMiddleware::class)->group(function() {

    Route::get('/available_crawlers' , 'availableCrawlers');

});