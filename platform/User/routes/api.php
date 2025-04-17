<?php

use Illuminate\Support\Facades\Route;
use Platform\User\Http\Controllers\Api\UserController;

Route::middleware(['api'])->prefix('api')->group(function () {
    Route::apiResource('users', UserController::class);
});
