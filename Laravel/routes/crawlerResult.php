<?php

use App\Http\Controllers\CrawlerResultController;
use Illuminate\Support\Facades\Route;

Route::prefix('/result')->middleware('auth')->controller(CrawlerResultController::class)->group(function(){

    Route::get('/' , 'index')->name('result.index');
});