<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::apiResource('product', ProductController::class);
Route::apiResource('order', OrderController::class)->only(['index', 'store', 'show']);
