<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiProductController;
use App\Http\Controllers\Api\ApiCustomerController;
use App\Http\Controllers\Api\ApiSaleController;
use App\Http\Controllers\Api\ApiPurchaseController;
use App\Http\Controllers\Api\ApiStockController;

/*
|--------------------------------------------------------------------------
| REST API Routes - Odoo Integration
|--------------------------------------------------------------------------
|
| File ini berisi seluruh route REST API yang berkomunikasi dengan Odoo 19.
| Alur: Odoo (JSON-RPC) → Laravel REST API → Client
|
| Base URL otomatis: /api/... (prefix ditambahkan oleh Laravel dari bootstrap/app.php)
|
| Semua route (kecuali login) akan diproteksi oleh middleware ApiTokenMiddleware
| setelah implementasi authentication selesai di tahap berikutnya.
|
*/

// ---------------------------------------------------------------
// Authentication (public - tidak butuh token)
// ---------------------------------------------------------------
Route::post('/login',   [ApiAuthController::class, 'login']);
Route::post('/logout',  [ApiAuthController::class, 'logout']);
Route::post('/refresh', [ApiAuthController::class, 'refresh']);
Route::get('/me',       [ApiAuthController::class, 'me']);

// ---------------------------------------------------------------
// Protected Routes (akan diproteksi token di tahap berikutnya)
// ---------------------------------------------------------------

// --- Product ---
Route::get('/products',          [ApiProductController::class, 'index']);
Route::get('/products/{id}',     [ApiProductController::class, 'show']);
Route::post('/products',         [ApiProductController::class, 'store']);
Route::put('/products/{id}',     [ApiProductController::class, 'update']);
Route::delete('/products/{id}',  [ApiProductController::class, 'destroy']);

// --- Customer ---
Route::get('/customers',         [ApiCustomerController::class, 'index']);
Route::get('/customers/{id}',    [ApiCustomerController::class, 'show']);
Route::post('/customers',        [ApiCustomerController::class, 'store']);
Route::put('/customers/{id}',    [ApiCustomerController::class, 'update']);
Route::delete('/customers/{id}', [ApiCustomerController::class, 'destroy']);

// --- Sales Order ---
Route::get('/sales',             [ApiSaleController::class, 'index']);
Route::get('/sales/{id}',        [ApiSaleController::class, 'show']);
Route::post('/sales',            [ApiSaleController::class, 'store']);
Route::put('/sales/{id}',        [ApiSaleController::class, 'update']);
Route::delete('/sales/{id}',     [ApiSaleController::class, 'destroy']);

// --- Purchase Order ---
Route::get('/purchases',         [ApiPurchaseController::class, 'index']);
Route::get('/purchases/{id}',    [ApiPurchaseController::class, 'show']);
Route::post('/purchases',        [ApiPurchaseController::class, 'store']);
Route::put('/purchases/{id}',    [ApiPurchaseController::class, 'update']);
Route::delete('/purchases/{id}', [ApiPurchaseController::class, 'destroy']);

// --- Inventory / Stock ---
Route::get('/stocks',            [ApiStockController::class, 'index']);
Route::get('/stocks/{id}',       [ApiStockController::class, 'show']);
Route::post('/stocks',           [ApiStockController::class, 'store']);
Route::put('/stocks/{id}',       [ApiStockController::class, 'update']);
Route::delete('/stocks/{id}',    [ApiStockController::class, 'destroy']);
