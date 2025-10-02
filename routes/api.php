<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductsController as ApiProductsController;

Route::middleware(['apitoken'])->group(function () {
    Route::get('/products', [ApiProductsController::class, 'index']);
});
