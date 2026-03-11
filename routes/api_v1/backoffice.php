<?php

use App\Http\Controllers\Api\BackOffice\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| BackOffice API Routes (v1)
|--------------------------------------------------------------------------
|
| Prefix: /api/v1/backoffice
| Middleware: api
|
*/

// --- Public ---
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// --- Authenticated (auth:agent) ---
Route::middleware('auth:agent')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // --- Permission protected (agent.access) ---
    Route::middleware('agent.access')->group(function () {
        // GET  /orders
        // POST /products
    });
});
