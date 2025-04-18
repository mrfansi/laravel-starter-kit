<?php

use Illuminate\Support\Facades\Route;
use Platform\Admin\Http\Controllers\AdminController;
use Platform\Admin\Http\Controllers\Auth\LoginController;
use Platform\Admin\Http\Controllers\DashboardController;
use Platform\Admin\Http\Controllers\RoleController;
use Platform\Admin\Http\Controllers\SettingController;
use Platform\Admin\Http\Controllers\UserController;

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes
    Route::middleware('web')->group(function () {
        // Authentication routes
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
    });
    
    // Authenticated routes
    Route::middleware(['web', 'admin.auth'])->group(function () {
        // Logout route
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Users management
        Route::middleware('admin.permission:user.view')->group(function () {
            Route::resource('users', UserController::class);
        });
        
        // Roles & Permissions management
        Route::middleware('admin.permission:role.view')->group(function () {
            Route::resource('roles', RoleController::class);
            Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
            Route::put('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
        });
        
        // Settings
        Route::middleware('admin.permission:admin.settings')->group(function () {
            Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
            Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        });
    });
});
