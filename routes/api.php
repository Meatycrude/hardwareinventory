<?php

use App\Http\Controllers\Api\AutheticationController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {

    Route::post('/login', [AutheticationController::class, 'store'])->name('login');

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/products', [ProductController::class, 'store']);

    Route::get('/products', [ProductController::class, 'index']);

    Route::get('/products/{product}', [ProductController::class, 'show']);

    Route::put('/products/{product}', [ProductController::class, 'update']);

    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    Route::post('/sales', [SaleController::class, 'store']);

    Route::get('/sales', [SaleController::class, 'index']);

    Route::get('/sales/{sale}', [SaleController::class, 'show']);

    Route::post('/categories', [CategoryController::class, 'store']);

    Route::get('/categories', [CategoryController::class, 'index']);

    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    Route::post('/suppliers', [SupplierController::class, 'store']);

    Route::get('/suppliers', [SupplierController::class, 'index']);

    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show']);

    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update']);

    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy']);

    Route::get('/stock-movements', [StockMovementController::class, 'index']);

    Route::get('/products/{product}/movements', [StockMovementController::class, 'productMovements']);

    Route::get('/dashboard/stats', [DashboardController::class, 'index']);
});
