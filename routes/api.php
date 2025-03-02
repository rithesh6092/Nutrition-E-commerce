<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class,'adminLogin']);

Route::resource('customers', CustomerController::class);
Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);
Route::put('/products/status/{product}', [ProductController::class, 'updateProductStatus']);


// Order routes
Route::apiResource('orders', OrderController::class);
Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
Route::patch('orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus']);

