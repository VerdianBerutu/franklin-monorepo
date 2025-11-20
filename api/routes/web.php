<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product; // Tambahkan ini

// Default Laravel welcome page
Route::get('/', function () {
    return view('welcome');
});

// Route untuk login - redirect ke frontend Vue
Route::get('/login', function () {
    return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/login');
})->name('login');

// 🔧 TEST ROUTE - Debug Product Model
Route::get('/test-product-model', function() {
    try {
        echo "<h1>Testing Product Model</h1>";
        
        // Test 1: Class existence
        if (class_exists('App\Models\Product')) {
            echo "✅ <strong>Class App\Models\Product exists</strong><br>";
        } else {
            echo "❌ <strong>Class App\Models\Product NOT found</strong><br>";
            return;
        }

        // Test 2: File path
        try {
            $reflector = new ReflectionClass('App\Models\Product');
            echo "✅ <strong>File location:</strong> " . $reflector->getFileName() . "<br>";
        } catch (Exception $e) {
            echo "❌ <strong>Reflection error:</strong> " . $e->getMessage() . "<br>";
        }

        // Test 3: Database connection
        try {
            $count = Product::count();
            echo "✅ <strong>Product count:</strong> " . $count . "<br>";
            
            // Test 4: Try to get all products
            $products = Product::all();
            echo "✅ <strong>Products retrieved:</strong> " . $products->count() . "<br>";
            
        } catch (Exception $e) {
            echo "❌ <strong>Database error:</strong> " . $e->getMessage() . "<br>";
            
            // Check if table exists
            try {
                \DB::select('SELECT 1 FROM products LIMIT 1');
                echo "✅ <strong>Table 'products' exists</strong><br>";
            } catch (Exception $e2) {
                echo "❌ <strong>Table 'products' doesn't exist:</strong> " . $e2->getMessage() . "<br>";
            }
        }

    } catch (Error $e) {
        echo "❌ <strong>Fatal error:</strong> " . $e->getMessage() . "<br>";
        echo "<strong>File:</strong> " . $e->getFile() . "<br>";
        echo "<strong>Line:</strong> " . $e->getLine() . "<br>";
    }
});
// Route untuk halaman Edit Profile (Vue SPA)
Route::get('/profile', function () {
    return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/profile');
})->name('profile.edit');
// Tambahkan routes web lainnya di sini jika ada
// Contoh:
// Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
// Route::get('/about', [AboutController::class, 'index']);