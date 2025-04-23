<?php

use Illuminate\Support\Facades\Route;
use Platform\Landing\Http\Controllers\LandingController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('landing', LandingController::class)->names('landing');
});
