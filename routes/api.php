<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/catalogs', [\App\Http\Controllers\Api\CatalogController::class, 'index']);
Route::get('/catalogs/{slug}', [\App\Http\Controllers\Api\CatalogController::class, 'show']);

Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index']);
Route::get('/products/{slug}', [\App\Http\Controllers\Api\ProductController::class, 'show']);
