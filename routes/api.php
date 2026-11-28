<?php

use App\Http\Controllers\MobileController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile/v1')->middleware('throttle:120,1')->group(function () {
    Route::get('catalog', [MobileController::class, 'catalog']);
    Route::post('register', [MobileController::class, 'register'])->middleware('throttle:6,1');
    Route::post('login', [MobileController::class, 'login'])->middleware('throttle:6,1');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [MobileController::class, 'me']);
        Route::post('logout', [MobileController::class, 'logout']);
        Route::post('reservations', [MobileController::class, 'reserve'])->middleware('throttle:20,1');
    });
});
