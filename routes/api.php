<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1,api-login')->name('api.login');

Route::middleware('throttle:60,1,api')->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('api.products.index');
    Route::get('/products/{id}', [ProductController::class, 'show'])->whereNumber('id')->name('api.products.show');

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
        Route::post('/orders', [OrderController::class, 'store'])->name('api.orders.store');
    });
});
