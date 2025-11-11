<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UploadController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\CertificateController;
use App\Http\Controllers\API\DashboardController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::apiResource('uploads', UploadController::class);
    Route::apiResource('sales', SaleController::class);
    Route::post('/sales/export', [SaleController::class, 'export']);
    Route::apiResource('certificates', CertificateController::class);
    Route::get('/certificates/expiring', [CertificateController::class, 'expiring']);
    Route::apiResource('customers', \App\Http\Controllers\API\CustomerController::class);
    Route::apiResource('products', \App\Http\Controllers\API\ProductController::class);
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', \App\Http\Controllers\API\UserController::class);
    });
});
