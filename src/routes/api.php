<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers')->group(function () {
    Route::post('/subscribe', 'OlxController@subscribe');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
