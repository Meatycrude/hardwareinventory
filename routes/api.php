<?php

use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\AutheticationController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MpesaController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UserController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {

    Route::post('/login', [AutheticationController::class, 'login'])
        ->name('login');

    Route::post('/verify-2fa', [AutheticationController::class, 'verifyTwoFactor'])
        ->name('verify-2fa');

    Route::post('/logout', [AutheticationController::class, 'destroy'])
        ->name('logout')
        ->middleware('auth:sanctum');

    Route::get('/user', fn (Request $request) => new UserResource($request->user()))
        ->middleware('auth:sanctum');

    Route::post('/products', [ProductController::class, 'store']);

    Route::get('/products', [ProductController::class, 'index']);

    Route::get('/products/{product}', [ProductController::class, 'show']);

    Route::put('/products/{product}', [ProductController::class, 'update']);

    Route::post('/products/{product}/restock', [ProductController::class, 'restock'])
        ->middleware(['auth:sanctum', 'role:admin,storekeeper']);

    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    Route::post('/sales', [SaleController::class, 'store'])
        ->middleware(['auth:sanctum', 'role:admin,cashier']);

    Route::get('/sales', [SaleController::class, 'index']);

    Route::get('/sales/{sale}', [SaleController::class, 'show']);

    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt']);

    Route::post('/sales/{sale}/void', [SaleController::class, 'void'])
        ->middleware(['auth:sanctum', 'role:admin,cashier']);

    Route::post('/categories', [CategoryController::class, 'store']);

    Route::get('/categories', [CategoryController::class, 'index']);

    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    Route::post('/suppliers', [SupplierController::class, 'store']);

    Route::get('/suppliers', [SupplierController::class, 'index']);

    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show']);

    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update']);

    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy']);

    Route::get('/stock-movements', [StockMovementController::class, 'index'])
        ->middleware(['auth:sanctum', 'role:admin,storekeeper']);

    Route::get('/products/{product}/movements', [StockMovementController::class, 'productMovements']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/dashboard/sales-trend', [DashboardController::class, 'salesTrend']);

    Route::get('/profile', [ProfileController::class, 'show'])
        ->middleware('auth:sanctum');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->middleware('auth:sanctum');

    Route::get('/users', [UserController::class, 'index'])
        ->middleware(['auth:sanctum', 'role:admin']);

    Route::post('/users', [UserController::class, 'store'])
        ->middleware(['auth:sanctum', 'role:admin']);

    Route::get('/mpesa/token', [MpesaController::class, 'token']);

    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->middleware(['auth:sanctum', 'role:admin']);

    Route::get('/dashboard/recent-activity', [DashboardController::class, 'recentActivity'])
        ->middleware(['auth:sanctum', 'role:admin']);

});
