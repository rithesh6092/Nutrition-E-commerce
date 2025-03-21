<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::post('/login', [AuthController::class,'adminLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verifyOTP', [AuthController::class, 'verifyOTP']);

Route::resource('customers', CustomerController::class);
Route::put('/customers/status/{customer}', [CustomerController::class, 'updateCustomerStatus']);
Route::resource('categories', CategoryController::class);
Route::put('/categories/status/{category}', [CategoryController::class, 'updateCategoryStatus']);
Route::resource('products', ProductController::class);
Route::put('/products/status/{product}', [ProductController::class, 'updateProductStatus']);

// Review routes
Route::apiResource('reviews', ReviewController::class);
Route::patch('reviews/{review}/status', [ReviewController::class, 'updateStatus']);
Route::get('product-reviews/{productId}', [ReviewController::class, 'getProductReviews']);
Route::get('top-rated-products', [ReviewController::class, 'getTopRatedProducts']);

// Order routes
Route::apiResource('orders', OrderController::class);
Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
Route::patch('orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus']);

//web routes
Route::get('/active-categories', [CategoryController::class, 'webCategories']);
Route::get('/productsbycategory/{category}', [ProductController::class,'productbycategory']);


