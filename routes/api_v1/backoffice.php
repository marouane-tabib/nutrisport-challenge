<?php

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
// POST /auth/login

// --- Authenticated (auth:agent) ---
// POST /auth/logout
// POST /auth/refresh

// --- Permission protected (agent.access) ---
// GET  /orders
// POST /products
