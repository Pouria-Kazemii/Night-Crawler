<?php

use App\Http\Controllers\CrawlerNodeController;
use Illuminate\Support\Facades\Route;

Route::prefix('/crawler-nodes')->middleware('auth')->controller(CrawlerNodeController::class)->group(function() {

    Route::get('/' , 'index')->name('crawl-nodes.index');
    Route::get('/create' , 'create')->name('crawl-nodes.create');
    Route::post('/' , 'store')->name('crawl-nodes.store');
    Route::get('/{crawlerNode}/edit' , 'edit')->name('crawl-nodes.edit');
    Route::put('/{crawlerNode}' , 'update')->name('crawl-nodes.update');
    Route::delete('/{crawlerNode}' , 'destroy')->name('crawl-nodes.destroy');
    Route::get('{crawlerNode}/ping' , 'pingNode' )->name('crawl-nodes.ping');

});