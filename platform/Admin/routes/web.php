<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Platform\Admin\Http\Controllers\Auth\LoginController;
use Platform\Admin\Http\Controllers\DashboardController;
use Platform\Admin\Http\Controllers\RoleController;
use Platform\Admin\Http\Controllers\SettingController;
use Platform\Admin\Http\Controllers\UserController;
use Platform\Admin\Http\Controllers\ConfigController;

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
        
        // Configurations
        Route::middleware('admin.permission:admin.settings')->group(function () {
            Route::resource('configurations', ConfigController::class)->names([
                'index' => 'config.index',
                'create' => 'config.create',
                'store' => 'config.store',
                'edit' => 'config.edit',
                'update' => 'config.update',
                'destroy' => 'config.destroy',
            ])->parameters([
                'configurations' => 'config'
            ]);
            Route::post('configurations/sync-to-env', [ConfigController::class, 'syncToEnv'])->name('config.sync-to-env');
            Route::post('configurations/sync-from-env', [ConfigController::class, 'syncFromEnv'])->name('config.sync-from-env');
        });
    });
});
