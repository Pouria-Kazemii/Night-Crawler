<?php

use App\Http\Controllers\CustomersController;
use Illuminate\Support\Facades\Route;

Route::prefix('/customers')->controller(CustomersController::class)->middleware('auth')->group(function () {

    // Main CRUD
    Route::get('/', 'index')->name('customer.index');
    Route::get('/create', 'create')->name('customer.create');
    Route::post('/', 'store')->name('customer.store');
    Route::get('/update/{customer}', 'edit')->name('customer.edit');
    Route::put('/{customer}', 'update')->name('customer.update');
    Route::delete('/{customer}', 'delete')->name('customer.delete');

    // Resubscribe 
    Route::patch('/{customer}', 'resubscribe')->name('customer.resubscribe');
});
