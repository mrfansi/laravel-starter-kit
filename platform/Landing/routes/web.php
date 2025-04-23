<?php

use Illuminate\Support\Facades\Route;
use Platform\Landing\Http\Controllers\LandingController;

Route::get('/', LandingController::class)->name('home');
