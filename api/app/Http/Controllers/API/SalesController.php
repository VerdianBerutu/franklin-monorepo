<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;    
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Sale::with(['customer', 'user', 'items.product']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('dateRange')) {
            $dates = explode(' to ', $request->dateRange);
            if (count($dates) === 2) {
                $query->whereBetween('sale_date', [$dates[0], $dates[1]]);
            }
        }

        $sales = $query->orderBy('created_at', 'desc')
                       ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $sales,
            'message' => 'Sales retrieved successfully'
        ]);
    }

    public function statistics(): JsonResponse
    {
        $totalRevenue = Sale::where('payment_status', 'paid')->sum('total');
        $totalTransactions = Sale::count();
        $productsSold = SaleItem::sum('quantity');
        $thisMonthRevenue = Sale::where('payment_status', 'paid')
            ->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->sum('total');

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => (float) $totalRevenue,
                'total_transactions' => (int) $totalTransactions,
                'products_sold' => (int) $productsSold,
                'this_month_revenue' => (float) $thisMonthRevenue,
            ]
        ]);
    }

    public function show($id): JsonResponse
    {
        $sale = Sale::with(['customer', 'user', 'items.product'])->find($id);

        if (!$sale) {
            return response()->json(['success' => false, 'message' => 'Sale not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $sale
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // Log request untuk debugging
        \Log::info('Sale Store Request:', $request->all());

        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'sale_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,card',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // 🔥 SOLUSI BARU: Generate invoice number dalam transaction yang sama
            $invoiceNumber = $this->generateInvoiceInTransaction();

            // Hitung subtotal dari items
            $subtotal = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $itemSubtotal = $item['qty'] * $item['price'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $itemSubtotal
                ];
            }

            $tax = $request->tax ?? 0;
            $discount = $request->discount ?? 0;
            $totalAmount = $subtotal + $tax - $discount;

            // Log sebelum create
            \Log::info('Creating sale with data:', [
                'invoice' => $invoiceNumber,
                'customer_id' => $request->customer_id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $totalAmount,
                'items_count' => count($itemsData)
            ]);

            // Buat sale record
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $request->customer_id,
                'sale_date' => $request->sale_date,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
               'total_amount'   => $totalAmount,
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'user_id' => auth()->id()
            ]);

            // Buat sale items
            $sale->items()->createMany($itemsData);

            DB::commit();

            // Log setelah berhasil
            \Log::info('Sale created successfully:', [
                'id' => $sale->id,
                'invoice' => $sale->invoice_number,
                'total' => $sale->total,
                'items' => $sale->items->count()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Penjualan berhasil disimpan!',
                'data' => $sale->load(['customer', 'items.product'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Sale creation failed:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan penjualan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate invoice number dalam transaction dengan retry logic
     */
    private function generateInvoiceInTransaction(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $maxRetries = 5;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            // Cari invoice terakhir dengan pattern yang sama
            $lastSale = Sale::withTrashed()
                ->where('invoice_number', 'like', "{$prefix}-{$date}-%")
                ->orderBy('invoice_number', 'desc')
                ->first();

            $nextNumber = $lastSale ? ((int) substr($lastSale->invoice_number, -4)) + 1 : 1;
            $invoiceNumber = sprintf('%s-%s-%04d', $prefix, $date, $nextNumber);

            // Check jika invoice number sudah ada (double check)
            $exists = Sale::withTrashed()
                ->where('invoice_number', $invoiceNumber)
                ->exists();

            if (!$exists) {
                return $invoiceNumber;
            }

            // Tunggu sebentar sebelum retry
            if ($attempt < $maxRetries) {
                usleep(100000 * $attempt); // 100ms, 200ms, 300ms, etc.
            }
        }

        throw new \Exception('Gagal generate invoice number unik setelah ' . $maxRetries . ' percobaan');
    }

    public function destroy($id): JsonResponse
    {
        $sale = Sale::find($id);
        
        if (!$sale) {
            return response()->json([
                'success' => false, 
                'message' => 'Penjualan tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $sale->items()->delete();
            $sale->delete();
            
            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Penjualan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus penjualan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printInvoice($id)
    {
        $sale = Sale::with(['customer', 'items.product', 'user'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.invoice', compact('sale'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Invoice_' . $sale->invoice_number . '.pdf');
    }
}