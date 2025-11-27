<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $sale->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { 
            font-family: 'DejaVu Sans', 'Inter', sans-serif; 
            margin: 0;
            padding: 0;
        }
        .border-dashed { border-style: dashed; }
        
        /* Fallback fonts untuk PDF */
        * {
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="max-w-4xl mx-auto bg-white shadow-2xl my-10 p-10">

    <!-- Header -->
    <div class="flex justify-between items-start mb-10">
        <div>
            <!-- Placeholder untuk logo -->
            <div class="h-16 mb-4 bg-gray-200 flex items-center justify-center">
                <span class="text-gray-600 font-bold text-lg">FRANKLIN SYSTEM</span>
            </div>
            <h1 class="text-4xl font-bold text-gray-800">INVOICE</h1>
            <p class="text-xl text-gray-600">{{ $sale->invoice_number }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500">Tanggal Invoice</p>
            <p class="text-xl font-semibold">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d F Y') }}</p>
            <p class="text-sm text-gray-500 mt-4">Metode Pembayaran</p>
            <p class="text-lg font-medium">{{ ucfirst($sale->payment_method) }}</p>
            <p class="text-sm text-gray-500 mt-2">Status</p>
            <p class="text-lg font-medium {{ $sale->payment_status === 'paid' ? 'text-green-600' : 'text-orange-600' }}">
                {{ ucfirst($sale->payment_status) }}
            </p>
        </div>
    </div>

    <!-- Bill To & From -->
    <div class="grid grid-cols-2 gap-10 mb-10">
        <div>
            <h3 class="font-bold text-gray-700 mb-2">Ditagihkan Kepada:</h3>
            <p class="font-semibold text-lg">{{ $sale->customer->name ?? 'Umum' }}</p>
            @if($sale->customer && $sale->customer->company)
                <p class="text-gray-600">{{ $sale->customer->company }}</p>
            @endif
            @if($sale->customer && $sale->customer->email)
                <p class="text-gray-600">{{ $sale->customer->email }}</p>
            @endif
            @if($sale->customer && $sale->customer->phone)
                <p class="text-gray-600">{{ $sale->customer->phone }}</p>
            @endif
        </div>
        <div class="text-right">
            <h3 class="font-bold text-gray-700 mb-2">Dari:</h3>
            <p class="font-bold text-lg">PT FRANKLIN INDONESIA</p>
            <p class="text-gray-600">Jakarta • Indonesia</p>
            <p class="text-gray-600">Telp: 021-12345678</p>
            <p class="text-gray-600">Kasir: {{ $sale->user->name ?? 'System' }}</p>
        </div>
    </div>

    <!-- Tabel Produk -->
    <table class="w-full mb-8 border border-gray-300">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left p-4 font-semibold">Produk</th>
                <th class="text-center p-4 font-semibold">Qty</th>
                <th class="text-right p-4 font-semibold">Harga</th>
                <th class="text-right p-4 font-semibold">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr class="border-t border-gray-200">
                <td class="p-4">
                    <div class="font-medium">{{ $item->product->name ?? 'Produk Tidak Ditemukan' }}</div>
                    @if($item->product && $item->product->sku)
                        <div class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</div>
                    @endif
                </td>
                <td class="p-4 text-center">{{ $item->quantity }}</td>
                <td class="p-4 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="p-4 text-right font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Di bagian total, ganti dengan ini: -->
<div class="flex justify-end mb-10">
    <div class="w-80">
        <div class="flex justify-between py-2">
            <span class="font-medium">Subtotal</span>
            <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
        </div>
        
        @if($sale->tax_rate > 0)
        <div class="flex justify-between py-2">
            <span class="font-medium">Pajak ({{ $sale->tax_rate }}%)</span>
            <span>Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        
        @if($sale->discount > 0)
        <div class="flex justify-between py-2">
            <span class="font-medium">Diskon</span>
            <span class="text-red-600">-Rp {{ number_format($sale->discount, 0, ',', '.') }}</span>
        </div>
        @endif
        
        <div class="flex justify-between py-3 text-2xl font-bold border-t-4 border-gray-800 pt-4 mt-2">
            <span>TOTAL</span>
            <span class="text-blue-600">Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

    <!-- Notes -->
    @if($sale->notes)
    <div class="mt-8 p-4 bg-gray-50 rounded-lg">
        <h4 class="font-bold text-gray-700 mb-2">Catatan:</h4>
        <p class="text-gray-600">{{ $sale->notes }}</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="text-center text-xs text-gray-500 mt-16 pt-8 border-t border-gray-300">
        Terima kasih atas kepercayaan Anda • Franklin Management System © {{ date('Y') }}
    </div>

</div>
</body>
</html>