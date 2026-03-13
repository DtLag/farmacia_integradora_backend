<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Rutas Publicas ---

// Staff
Route::post('/login/staff', [AuthController::class, 'loginStaff']);

// Clientes
Route::post('/register/customer', [AuthController::class, 'registerCustomer']);
Route::post('/login/customer', [AuthController::class, 'loginCustomer']);

// --- Rutas Protegidas (Requieren Token) ---

Route::middleware('auth:sanctum')->group(function () {
    // Gestion de Usuarios (Solo Admin)
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::post('/users/{id}/restore', [UserController::class, 'restore']);
    Route::post('/register/staff', [AuthController::class, 'registerStaff']);

    // Gestion de Proveedores (CU-24)
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);
    Route::post('/suppliers/{id}/restore', [SupplierController::class, 'restore']);

    // Ejemplo: Obtener el usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

