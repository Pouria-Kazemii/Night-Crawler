<?php

use Illuminate\Support\Facades\Route;
use Elastic\Elasticsearch\ClientBuilder;


Route::get('/', function () {
    return view('welcome');
});
