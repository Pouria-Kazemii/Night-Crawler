<?php

use App\Http\Middleware\VerifyCrawlerToken;
use App\Services\CrawlerManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/crawled-result' , function(Request $request) {

    $crawlerManager = app(CrawlerManager::class);
    
    $crawlerManager->discernment($request);

})->middleware(VerifyCrawlerToken::class);
