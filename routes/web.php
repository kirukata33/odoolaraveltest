<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// ---------------------------------------------------------------
// Redirect root ke login
// ---------------------------------------------------------------
Route::get('/', function () {
    return redirect()->route('login');
});

// ---------------------------------------------------------------
// Auth Routes (Guest only)
// ---------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Logout (auth required)
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ---------------------------------------------------------------
// Admin Routes (Auth required)
// ---------------------------------------------------------------
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
});

// ---------------------------------------------------------------
// Odoo Integration - Purchase Orders (Auth required)
// ---------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])
        ->name('purchase-orders.index');

    Route::get('/api/purchase-orders', [PurchaseOrderController::class, 'apiIndex'])
        ->name('purchase-orders.api');
});
