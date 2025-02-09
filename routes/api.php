<?php

use App\Http\Controllers\AndroidTvController;
use App\Http\Controllers\Auth\LoginUserController;
use App\Http\Controllers\Auth\RegisterUserController;
use App\Http\Middleware\LogActivityMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([LogActivityMiddleware::class])->group(function () {
    Route::post('/register', RegisterUserController::class)->name('register');
    Route::post('/login', LoginUserController::class)->name('login');

    Route::middleware('auth:api')->group(function () {
        Route::controller(AndroidTvController::class)->group(function () {
            Route::post('generate-tv-code', 'generateTvCode')->name('generate-tv-code');
            Route::post('active-tv-code', 'activeTvCode')->name('active-tv-code');
            Route::middleware('throttle:10,1')->post('poll-tv-code', 'pollTvCode')->name('poll-tv-code');
        });
    });
});
