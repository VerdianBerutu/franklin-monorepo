<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UploadController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\CertificateController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CustomerController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    
    // TEMPORARY: COMMENT SEMUA MIDDLEWARE PERMISSION
    Route::apiResource('users', UserController::class); //->middleware('can:view users');
    Route::apiResource('products', ProductController::class); //->middleware('can:view products');
    Route::apiResource('customers', CustomerController::class); //->middleware('can:view customers');
    Route::apiResource('sales', SaleController::class); //->middleware('can:view sales');
    Route::post('/sales/export', [SaleController::class, 'export']); //->middleware('can:export reports');
    Route::apiResource('certificates', CertificateController::class); //->middleware('can:view certificates');
    Route::get('/certificates/expiring', [CertificateController::class, 'expiring']); //->middleware('can:view certificates');
    Route::apiResource('uploads', UploadController::class); //->middleware('can:view uploads');
});