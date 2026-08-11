<?php

// Tambahkan baris-baris ini ke dalam routes/web.php project Laravel kamu

use App\Http\Controllers\PurchaseOrderController;

Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])
    ->name('purchase-orders.index');

// Endpoint API JSON (opsional, kalau nanti mau dikonsumsi app lain)
Route::get('/api/purchase-orders', [PurchaseOrderController::class, 'apiIndex'])
    ->name('purchase-orders.api');
