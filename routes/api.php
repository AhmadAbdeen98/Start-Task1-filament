<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Task 1 — Product & Category Management REST API.
| These routes are automatically prefixed with /api by Laravel.
|
*/

Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
