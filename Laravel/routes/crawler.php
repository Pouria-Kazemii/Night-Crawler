<?php

use App\Http\Controllers\CrawlerController;
use Illuminate\Support\Facades\Route;

Route::prefix('/crawler')->middleware('auth')->controller(CrawlerController::class)->group(function(){

    Route::get('/' , 'index')->name('crawler.index');
    Route::get('/create' , 'create')->name('crawler.create');
    Route::post('/' , 'store')->name('crawler.store');
    Route::get('/{crawler}/edit' , 'edit')->name('crawler.edit');
    Route::put('/{crawler}' , 'update')->name('crawler.update');
    Route::delete('/{crawler}' , 'destroy')->name('crawler.destroy');
    Route::post('/{crawler}/go' , 'go')->name('crawler.go');
});