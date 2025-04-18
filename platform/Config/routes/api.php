<?php

use Illuminate\Support\Facades\Route;
use Platform\Config\Http\Controllers\Api\ConfigurationController;

Route::middleware(['api'])->prefix('api/config')->group(function () {
    // Public routes - accessible without authentication
    Route::get('/', [ConfigurationController::class, 'index']);
    Route::get('/{key}', [ConfigurationController::class, 'show']);
    
    // Admin routes - require authentication
    Route::middleware(['auth:api'])->group(function () {
        Route::put('/{key}', [ConfigurationController::class, 'update']);
        Route::post('/batch', [ConfigurationController::class, 'batchUpdate']);
    });
});
