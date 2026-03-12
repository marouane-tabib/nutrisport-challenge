<?php

use App\Http\Controllers\Api\Client\AuthController;
use App\Http\Controllers\Api\Client\ProductController;
use App\Http\Controllers\Api\Feed\ProductFeedController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Client API Routes
|--------------------------------------------------------------------------
|
| Prefix: /api
| resolve.site middleware applied to site-scoped routes only
|
*/

// --- Site-scoped routes ---
Route::middleware('resolve.site')->group(function () {

    // Public (no auth)
    Route::put('/auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    // Catalog
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Cart (no auth required)
    // POST /cart/items
    // GET  /cart/{cartId}
    // DELETE /cart/{cartId}/items/{productId}

    // Authenticated
    Route::middleware('auth:client')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::put('/auth/password', [AuthController::class, 'updatePassword']);

        // Orders
        // POST /orders
        // GET  /orders
    });
});

// --- Public routes (no site-scoping) ---
Route::get('/feeds/products.{format}', [ProductFeedController::class, 'index'])
    ->whereIn('format', ['json', 'xml']);
