<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\InventoryAdjustmentController;
use App\Http\Controllers\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PickUpController;
use App\Http\Controllers\ProcessOrderPickUpController;
use App\Http\Controllers\UserController;

// --- Rutas Públicas ---

// Staff
Route::post('/login/staff', [AuthController::class, 'loginStaff']);

Route::delete('/delete/{id}', [ProductsController::class, 'delete']);

Route::get('/alert/stock', [AlertController::class, 'lowStock']);
Route::get('/alert/expire', [AlertController::class, 'expireSoon']);
Route::get('/alert/expired', [AlertController::class, 'expired']);

Route::put('adjustment/{id}', [InventoryAdjustmentController::class, 'alter']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// Clientes
Route::post('/register/customer', [AuthController::class, 'registerCustomer']);
Route::post('/login/customer', [AuthController::class, 'loginCustomer']);


// --- Rutas Protegidas (Requieren Token) ---

Route::middleware('auth:sanctum')->group(function () {

    // Gestión de Usuarios (Solo Admin)
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::post('/users/{id}/restore', [UserController::class, 'restore']);
    Route::post('/register/staff', [AuthController::class, 'registerStaff']);

    // Ejemplo: Obtener el usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/create/pick-up/order', [PickUpController::class, 'store']);
});

Route::get('/pickup/orders/pending', [ProcessOrderPickUpController::class, 'indexPending']);

Route::patch('/pickup/orders/{id}/start', [ProcessOrderPickUpController::class, 'startPreparation']);

Route::patch('/pickup/orders/{id}/finish', [ProcessOrderPickUpController::class, 'finishPreparation']);

Route::post('/products', [ProductController::class, 'registerProduct']);
Route::patch('/products/{id}', [ProductController::class, 'update']);

Route::post('/register-batch-reception', [BatchController::class, 'registerBatchReception']);

Route::get('/products/search', [ProductController::class, 'search']);
Route::post('/sales', [SaleController::class, 'store']);
Route::get('/sales/{id}/ticket', [SaleController::class, 'getTicket']);

Route::get('/categories', function () {
    return \App\Models\Category::all();
});

Route::get('/suppliers', function () {
    return \App\Models\Supplier::all();
});
