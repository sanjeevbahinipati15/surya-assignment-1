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

Route::get('/products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/update/{id}', [ProductController::class, 'update'])->name('products.update');
Route::get('/products/delete/{id}', [ProductController::class, 'delete'])->name('products.delete');


Route::get('/orders/print/{id}', [ProductController::class, 'printInvoice'])->name('orders.print');

