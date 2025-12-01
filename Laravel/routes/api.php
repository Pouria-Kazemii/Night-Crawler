<?php

use App\Http\Controllers\Api\ResultController;
use App\Http\Middleware\VerifyCrawlerToken;
use App\Http\Requests\CrawlerResultReceiveRequest;
use App\Jobs\ProcessCrawledResultJob;
use Illuminate\Support\Facades\Route;

Route::middleware(VerifyCrawlerToken::class)->group(function () {

    Route::post('/crawled-result', function (CrawlerResultReceiveRequest $request) {
        ProcessCrawledResultJob::dispatch($request->validated())->onConnection('crawler-receive')->onQueue('crawler-receive-jobs');
    });
    
});

require __DIR__ . '/customerServiceApi.php';
