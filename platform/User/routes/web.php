<?php

use Illuminate\Support\Facades\Route;
use Platform\User\Http\Controllers\UserController;

Route::middleware(['web'])->group(function () {
    Route::resource('users', UserController::class);
});
