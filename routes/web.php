<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductsController;

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
Route::get('/products/{sku}', [ProductsController::class, 'show'])->name('products.show');
