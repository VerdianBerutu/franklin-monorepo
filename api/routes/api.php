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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

//  PUBLIC ROUTES dengan Rate Limiting
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
});

//  AUTHENTICATED ROUTES
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    
    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])
        ->name('api.dashboard.stats');
    
    // ===============================================
    // USER MANAGEMENT
    // ===============================================
    Route::prefix('users')->name('api.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
    
    // ===============================================
    // PRODUCTS MANAGEMENT
    // ===============================================
    Route::prefix('products')->name('api.products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });
    
    // ===============================================
    // CUSTOMERS MANAGEMENT
    // ===============================================
    Route::prefix('customers')->name('api.customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
    });
    
    // ===============================================
    // SALES MANAGEMENT
    // ===============================================
    Route::prefix('sales')->name('api.sales.')->group(function () {
        //  PENTING: Custom routes SEBELUM resource routes
        Route::post('/export', [SaleController::class, 'export'])->name('export');
        
        // Standard CRUD
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::post('/', [SaleController::class, 'store'])->name('store');
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
        Route::put('/{sale}', [SaleController::class, 'update'])->name('update');
        Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
    });
    
    // ===============================================
    // CERTIFICATES MANAGEMENT -  
    // ===============================================
    Route::prefix('certificates')->name('api.certificates.')->group(function () {
        // Custom routes SEBELUM resource-like routes
        Route::get('/expiring', [CertificateController::class, 'expiring'])->name('expiring');
        Route::get('/my-certificates', [CertificateController::class, 'myCertificates'])->name('my-certificates');
        Route::get('/{certificate}/download', [CertificateController::class, 'download'])->name('download');
        Route::get('/{certificate}/preview', [CertificateController::class, 'preview'])->name('preview');
        
        // Standard CRUD routes
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::post('/', [CertificateController::class, 'store'])->name('store');
        Route::get('/{certificate}', [CertificateController::class, 'show'])->name('show');
        Route::put('/{certificate}', [CertificateController::class, 'update'])->name('update');
        Route::delete('/{certificate}', [CertificateController::class, 'destroy'])->name('destroy');
    });
    
    // ===============================================
    // UPLOADS/FILES MANAGEMENT
    // ===============================================
    Route::prefix('uploads')->name('api.uploads.')->group(function () {
        Route::get('/', [UploadController::class, 'index'])->name('index');
        Route::post('/', [UploadController::class, 'store'])->name('store');
        Route::get('/{upload}', [UploadController::class, 'show'])->name('show');
        Route::put('/{upload}', [UploadController::class, 'update'])->name('update');
        Route::delete('/{upload}', [UploadController::class, 'destroy'])->name('destroy');
    });
});