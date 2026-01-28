<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/fresh', function () {
    Artisan::call('migrate:fresh --seed');

    return response()->json('Database refreshed successfully');
});
