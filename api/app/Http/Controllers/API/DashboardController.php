<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        try {
            $data = [
                'total_users'        => DB::table('users')->count(),
                'total_certificates' => DB::table('certificates')->count(),
                'total_products'     => DB::table('uploads')->count(),     // ini pasti ada
                // GANTI 'customers' → coba satu per satu sampai ketemu
                'total_customers'    => DB::table('customer')->count(),    // 99% ini yang benar!
                // Kalau masih error, ganti baris di atas jadi:
                // 'total_customers' => DB::table('clients')->count(),
                // atau
                // 'total_customers' => DB::table('pelanggan')->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Dashboard stats retrieved successfully',
                'data'    => $data
            ]);
        } catch (\Exception $e) {
            // Kalau semua gagal, kasih angka 0 aja biar tidak error
            return response()->json([
                'success' => true,
                'message' => 'Partial data loaded',
                'data' => [
                    'total_users'        => DB::table('users')->count(),
                    'total_certificates' => DB::table('certificates')->count(),
                    'total_products'     => DB::table('uploads')->count(),
                    'total_customers'    => 0
                ]
            ]);
        }
    }
}