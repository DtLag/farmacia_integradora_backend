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
use App\Http\Controllers\AuditController;
use App\Http\Controllers\CancelPickUpOrderController;
use App\Http\Controllers\PickUpController;
use App\Http\Controllers\ProcessOrderPickUpController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompletePickUpController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

// RUTAS PÚBLICAS (No requieren autenticación)

// Autenticación Staff
Route::post('/login/staff', [AuthController::class, 'loginStaff']);

// Autenticación Clientes
Route::post('/login/customer', [AuthController::class, 'loginCustomer']);
Route::post('/register/customer', [AuthController::class, 'registerCustomer']);

// Recuperación y Verificación (Clientes)
Route::post('/verify-code', [VerificationController::class, 'verifyCode']);
Route::post('/resend-code', [VerificationController::class, 'resendCode']);
Route::post('customer/forgot-password', [CustomerController::class, 'forgotPassword']);
Route::post('customer/reset-password', [CustomerController::class, 'resetPassword']);
Route::post('customer/check-reset-code', [VerificationController::class, 'checkResetCode']);

// Catálogo público (Buscador y Categorías para la Landing Page)
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/categories/get', function () {
    return \App\Models\Category::all();
});

// RUTAS PRIVADAS (Requieren Token Sanctum)

Route::middleware('auth:sanctum')->group(function () {

    Route::get('payment/methods', [SaleController::class, 'getPaymentMethods']);
    Route::get('/pickup/order/{state}', [PickUpController::class, 'index']);


    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // RUTAS EXCLUSIVAS DEL STAFF Y ADMIN (Aplicar Middleware)
    Route::middleware('staff.only')->group(function () {
        
        // --- Gestión de Usuarios (Staff) ---
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::post('/users/{id}/restore', [UserController::class, 'restore']);
        Route::post('/register/staff', [AuthController::class, 'registerStaff']);
        Route::get('/staff', [UserController::class, 'staff']);

        // --- Gestión de Proveedores ---
        Route::get('/suppliers', [SupplierController::class, 'index']);
        Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
        Route::post('/suppliers', [SupplierController::class, 'store']);
        Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
        Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);
        Route::post('/suppliers/{id}/restore', [SupplierController::class, 'restore']);
        Route::get('/supply', function () {
            return \App\Models\Supplier::all();
        });

        // --- Gestión de Productos e Inventario ---
        Route::post('/products', [ProductController::class, 'registerProduct']);
        Route::patch('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/delete/{id}', [ProductsController::class, 'delete']);
        Route::apiResource('categories', CategoryController::class);
        
        Route::get('/inventory/products', [ProductController::class, 'inventory']);
        Route::get('/inventory/batches', [BatchController::class, 'inventory']);
        Route::post('/register-batch-reception', [BatchController::class, 'registerBatchReception']);
        Route::put('adjustment/{id}', [InventoryAdjustmentController::class, 'alter']);

        // --- Alertas y Auditorías ---
        Route::get('/alert/stock', [AlertController::class, 'lowStock']);
        Route::get('/alert/expire', [AlertController::class, 'expireSoon']);
        Route::get('/alert/expired', [AlertController::class, 'expired']);
        Route::get('/audits', [AuditController::class, 'index']);
        Route::get('/today/audits', [AuditController::class, 'todayAudits']);

        // --- Reportes y Métricas ---
        Route::get('reports/sales-and-orders', [SaleController::class, 'getAllSalesAndPickups']);
        Route::get('/report/sales', [ReportController::class, 'salesReport']);
        Route::get('/report/inventory', [ReportController::class, 'inventoryReport']);
        Route::get('/user/metrics', [ReportController::class, 'user_metrics']);

        // --- Proceso de Ventas (Punto de Venta) y Gestión de Pedidos ---
        Route::post('/sales', [SaleController::class, 'store']);
        Route::get('/sales/{id}/ticket', [SaleController::class, 'getTicket']);
        
        Route::patch('/pickup/orders/{id}/start', [ProcessOrderPickUpController::class, 'startPreparation']);
        Route::post('/pickup/{id}/complete', [CompletePickUpController::class, 'completeOrder']);
        Route::post('/pickup/{id}/cancel', [CancelPickUpOrderController::class, 'manualCancel']);

        // --- Gestión de Clientes (CRUD desde el Admin) ---
        Route::get('customer/{id}', [CustomerController::class, 'show']);
        Route::put('customer/{id}', [CustomerController::class, 'update']);
        Route::delete('customer/{id}', [CustomerController::class, 'destroy']);
    });

    // RUTAS DEL CLIENTE
    Route::post('/create/pick-up/order', [PickUpController::class, 'store']);
    Route::get('/report/restock-projection', [ReportController::class, 'restockProjection']);

});