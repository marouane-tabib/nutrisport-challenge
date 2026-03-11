<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Client API Routes (v1)
|--------------------------------------------------------------------------
|
| Prefix: /api/v1
| Middleware: api, resolve.site (except feeds)
|
*/

// --- Public (no auth) ---

// Auth
// POST /auth/register
// POST /auth/login

// Catalog
// GET /products
// GET /products/{id}

// Cart (no auth required)
// POST /cart/items
// GET  /cart/{cartId}
// DELETE /cart/{cartId}/items/{productId}

// Feeds (no site middleware needed)
// GET /feeds/products.{format}

// --- Authenticated (auth:client) ---

// POST /auth/logout
// POST /auth/refresh
// GET  /auth/profile
// PUT  /auth/profile
// PUT  /auth/password
// GET  /orders
// POST /orders
