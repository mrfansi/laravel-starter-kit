<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant API Routes
|--------------------------------------------------------------------------
|
| Here you can register API routes for your tenant applications.
| These routes are loaded by the TenantServiceProvider.
|
*/

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->prefix('api')->group(function () {
    Route::get('/user', function () {
        return auth()->user();
    })->middleware('auth:sanctum');
    
    // Contoh endpoint API tenant
    Route::get('/tenant-info', function () {
        return [
            'tenant_id' => tenant('id'),
            'tenant_name' => tenant()->name,
            'plan' => tenant()->plan,
        ];
    });
    
    // API Products
    Route::apiResource('products', \Platform\Tenant\Http\Controllers\Api\ProductController::class);
});
