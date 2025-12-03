<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WaterIntakeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        Route::get('/water-intake', [WaterIntakeController::class, 'index']);
        Route::post('/water-intake', [WaterIntakeController::class, 'store']);
    });
});

