<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('home');

Route::post('/products', [ProductController::class, 'store'])->name('products.store');
// Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::post('/cart/buy/{id}', [ProductController::class, 'buy'])->name('cart.buy');
Route::post('/cart/checkout', [ProductController::class, 'checkout'])->name('cart.checkout');

