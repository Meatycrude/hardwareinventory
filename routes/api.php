<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;

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
