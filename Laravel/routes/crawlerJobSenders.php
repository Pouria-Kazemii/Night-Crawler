<?php

use App\Http\Controllers\CrawlerJobSenderController;
use Illuminate\Support\Facades\Route;

Route::prefix('/sender')->middleware('auth')->controller(CrawlerJobSenderController::class)->group(function(){

    Route::get('/' , 'index')->name('sender.index');
});