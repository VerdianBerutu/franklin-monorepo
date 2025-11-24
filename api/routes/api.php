<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UploadController;
use App\Http\Controllers\API\SalesController;
use App\Http\Controllers\API\CertificateController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CustomerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// ==================== PUBLIC ROUTES ====================
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
});

// ==================== AUTHENTICATED ROUTES ====================
Route::middleware('auth:sanctum')->group(function () {

    // Logout & User Info
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');

    // DASHBOARD ROUTES — SUDAH AMAN DI DALAM AUTH!
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])
        ->name('api.dashboard.stats');
   Route::get('/dashboard/trend/{period?}', [DashboardController::class, 'trend'])
     ->where('period', '30days|annually');

    // USER MANAGEMENT
    Route::prefix('users')->name('api.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // PRODUCTS MANAGEMENT
    Route::prefix('products')->name('api.products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });

    // CUSTOMERS MANAGEMENT
    Route::prefix('customers')->name('api.customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
    });

   // SALES MANAGEMENT
Route::prefix('sales')->name('api.sales.')->group(function () {
    //  ADD THIS LINE - Statistics route MUST come BEFORE {sale}
    Route::get('/statistics', [SalesController::class, 'statistics'])->name('statistics');
    
    Route::post('/export', [SalesController::class, 'export'])->name('export');
    Route::get('/', [SalesController::class, 'index'])->name('index');
    Route::post('/', [SalesController::class, 'store'])->name('store');
    Route::get('/{sale}', [SalesController::class, 'show'])->name('show');
    Route::put('/{sale}', [SalesController::class, 'update'])->name('update');
    Route::delete('/{sale}', [SalesController::class, 'destroy'])->name('destroy');
    Route::get('/{sale}/print', [SalesController::class, 'printInvoice'])->name('sales.print');
});

    // CERTIFICATES MANAGEMENT
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/statistics', [CertificateController::class, 'statistics'])->name('statistics');
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::post('/', [CertificateController::class, 'store'])->name('store');
        Route::get('/{certificate}', [CertificateController::class, 'show'])->name('show');
        Route::post('/{certificate}', [CertificateController::class, 'update'])->name('update');
        Route::delete('/{certificate}', [CertificateController::class, 'destroy'])->name('destroy');
        Route::get('/{certificate}/download', [CertificateController::class, 'download'])->name('download');
        Route::get('/{certificate}/view', [CertificateController::class, 'view'])->name('view');
    });

    // UPLOADS MANAGEMENT
    Route::prefix('uploads')->name('api.uploads.')->group(function () {
        Route::get('/', [UploadController::class, 'index'])->name('index');
        Route::post('/', [UploadController::class, 'store'])->name('store');
        Route::get('/{upload}', [UploadController::class, 'show'])->name('show');
        Route::put('/{upload}', [UploadController::class, 'update'])->name('update');
        Route::delete('/{upload}', [UploadController::class, 'destroy'])->name('destroy');
    });

    // GANTI PASSWORD
    Route::post('/update-password', function (Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini salah.'],
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah!'
        ]);
    })->name('api.update-password');

});