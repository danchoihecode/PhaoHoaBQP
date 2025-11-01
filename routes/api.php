<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\FooterController;
use App\Http\Controllers\Api\TopMenuController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Admin\ProductController;

//auth
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/forgot_password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/verify_email', [AuthController::class, 'verifyEmail']);
Route::post('/auth/send_verify', [AuthController::class, 'sendVerify']);

Route::middleware(['auth:sanctum', \App\Http\Middleware\AuthUser::class])->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    //cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::get('/cart/items', [CartController::class, 'show']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    // Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::get('/cart/total', [CartController::class, 'viewCartAndCalculateTotal']);
    Route::post('/cart/submit', [CartController::class, 'submitCart']);

});

// products-user
Route::get('/products', [ProductController::class, 'index']);
// Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/{categorySlug}', [App\Http\Controllers\Api\ProductController::class, 'getProductsByCategorySlug']);
Route::get('/product-by-slug/{slug}', [App\Http\Controllers\Api\ProductController::class, 'getProductDetailsBySlug']);
Route::get('/flash-sale', [App\Http\Controllers\Api\ProductController::class, 'getFirstTwentyProducts']);
Route::post('/product/{id}/description', [App\Http\Controllers\Api\ProductController::class, 'updateDescription']);

//topmenu
Route::get('/topmenu', [TopMenuController::class, 'index']);

Route::get('/footer', [FooterController::class, 'index']);

//category
Route::get('/categories/name/{slug}', [CategoryController::class, 'getCategoryNameBySlug']);

//cart
Route::get('/cart/id', [CartController::class, 'getCartId']);
Route::post('/cart/add-item', [CartController::class, 'addToCart']);
Route::delete('/cart/delete-item', [CartController::class, 'removeFromCart']);
Route::post('/cart/increase-item', [CartController::class, 'increaseQuantity']);
Route::post('/cart/decrease-item', [CartController::class, 'decreaseQuantity']);
Route::post('/cart/update-item', [CartController::class, 'updateQuantity']);
Route::get('/cart/{id}', [CartController::class, 'getCart']);

//order
Route::post('/order', [OrderController::class, 'store']);
//store
Route::get('/stores', [StoreController::class, 'index']);


