<?php

declare(strict_types=1);

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products/seed', [ProductController::class, 'seed']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{product}', [ProductController::class, 'show'])->whereNumber('product');
Route::patch('/products/{product}', [ProductController::class, 'update'])->whereNumber('product');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->whereNumber('product');
