<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// --- Rutas Públicas ---

// Staff
Route::post('/login/staff', [AuthController::class, 'loginStaff']);

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
});

Route::post('/products', [ProductController::class, 'registerProduct']);
Route::patch('/products/{id}', [ProductController::class, 'update']);

Route::post('/register-batch-reception', [BatchController::class, 'registerBatchReception']);

Route::get('/products/search', [ProductController::class, 'search']);
Route::post('/sales', [SaleController::class, 'store']);
Route::get('/sales/{id}/ticket', [SaleController::class, 'getTicket']);
