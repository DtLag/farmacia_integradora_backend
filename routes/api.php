<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\InventoryAdjustmentController;
use App\Http\Controllers\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\SaleController;



Route::delete('/delete/{id}', [ProductsController::class, 'delete']);

Route::get('/alert/stock', [AlertController::class, 'lowStock']);
Route::get('/alert/expire', [AlertController::class, 'expireSoon']);
Route::get('/alert/expired', [AlertController::class, 'expired']);

Route::put('adjustment/{id}', [InventoryAdjustmentController::class, 'alter']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/products', [ProductController::class, 'registerProduct']);
Route::patch('/products/{id}', [ProductController::class, 'update']);

Route::post('/register-batch-reception', [BatchController::class, 'registerBatchReception']);

Route::get('/products/search', [ProductController::class, 'search']);
Route::post('/sales', [SaleController::class, 'store']);
Route::get('/sales/{id}/ticket', [SaleController::class, 'getTicket']);
