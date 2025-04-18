<?php

use Illuminate\Support\Facades\Route;
use Platform\Admin\Http\Controllers\Api\AdminController;

Route::middleware(['api'])->prefix('api')->group(function () {
    Route::apiResource('{module}s', AdminController::class);
});
