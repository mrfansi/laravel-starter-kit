<?php

use Illuminate\Support\Facades\Route;
use Platform\Config\Http\Controllers\ConfigurationController;

// Routes are now handled by the Admin module
// Route::prefix('admin/config')->name('config.')->middleware(['web', 'auth:admin'])->group(function () {
//     Route::get('/', [ConfigurationController::class, 'index'])->name('configurations.index');
//     Route::get('/create', [ConfigurationController::class, 'create'])->name('configurations.create');
//     Route::post('/', [ConfigurationController::class, 'store'])->name('configurations.store');
//     Route::get('/{configuration}/edit', [ConfigurationController::class, 'edit'])->name('configurations.edit');
//     Route::put('/{configuration}', [ConfigurationController::class, 'update'])->name('configurations.update');
//     Route::delete('/{configuration}', [ConfigurationController::class, 'destroy'])->name('configurations.destroy');
//     
//     Route::post('/import', [ConfigurationController::class, 'import'])->name('configurations.import');
//     Route::get('/export', [ConfigurationController::class, 'export'])->name('configurations.export');
// });
