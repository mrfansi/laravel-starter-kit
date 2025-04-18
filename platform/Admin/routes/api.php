<?php

use Illuminate\Support\Facades\Route;
use Platform\Admin\Http\Controllers\Api\AdminController;

Route::middleware(['api'])->prefix('api')->group(function () {
    Route::apiResource('admins', AdminController::class)->names([
        'index' => 'api.admins.index',
        'store' => 'api.admins.store',
        'show' => 'api.admins.show',
        'update' => 'api.admins.update',
        'destroy' => 'api.admins.destroy',
    ]);
});
