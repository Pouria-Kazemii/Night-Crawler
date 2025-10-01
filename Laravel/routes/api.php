<?php

use App\Http\Controllers\Api\ResultController;
use App\Http\Middleware\VerifyCrawlerToken;
use App\Jobs\ProcessCrawledResultJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/crawled-result', function (Request $request) {

    ProcessCrawledResultJob::dispatch($request->all())->onConnection('crawler-receive');

})->middleware(VerifyCrawlerToken::class);


Route::get('get-results', [ResultController::class , 'index'])->middleware(VerifyCrawlerToken::class);
Route::get('get-content', [ResultController::class , 'content'])->middleware(VerifyCrawlerToken::class);
