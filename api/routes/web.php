<?php

use Illuminate\Support\Facades\Route;

// Default Laravel welcome page
Route::get('/', function () {
    return view('welcome');
});

// Tambahkan routes web lainnya di sini jika ada
// Contoh:
// Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
// Route::get('/about', [AboutController::class, 'index']);