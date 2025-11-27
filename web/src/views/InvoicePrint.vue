<script setup>
import { onMounted } from 'vue'
import { useRoute } from 'vue-router'

// Ambil data dari URL (kita kirim dari modal)
const route = useRoute()
const sale = JSON.parse(decodeURIComponent(route.query.data))

onMounted(() => {
  // Auto print setelah halaman muncul
  setTimeout(() => window.print(), 500)
  // Optional: tutup tab setelah print
  window.onafterprint = () => window.close()
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 p-8 font-sans">
    <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden">
      <!-- Header Gradient -->
      <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white p-12 text-center">
        <h1 class="text-6xl font-bold tracking-tight">FRANKLIN</h1>
        <p class="text-3xl mt-2 opacity-90">INVOICE RESMI</p>
        <p class="text-4xl mt-4 font-mono">{{ sale.invoice_number }}</p>
      </div>

      <div class="p-12">
        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-12 mb-12">
          <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-8 rounded-2xl border-l-8 border-indigo-600">
            <h3 class="text-indigo-800 font-bold text-lg uppercase tracking-wider mb-4">Ditagihkan Kepada</h3>
            <p class="text-2xl font-bold text-gray-800">{{ sale.customer?.name || 'Umum' }}</p>
            <p class="text-gray-600 mt-2">{{ sale.customer?.company || '' }}</p>
            <p class="text-gray-600">{{ sale.customer?.email || '' }}</p>
            <p class="text-gray-600">{{ sale.customer?.phone || '' }}</p>
          </div>

          <div class="bg-gradient-to-br from-purple-50 to-pink-100 p-8 rounded-2xl border-r-8 border-purple-600 text-right">
            <h3 class="text-purple-800 font-bold text-lg uppercase tracking-wider mb-4">Informasi Invoice</h3>
            <p class="text-3xl font-bold">{{ $filters.formatDate(sale.sale_date) }}</p>
            <p class="text-xl mt-3">Kasir: <span class="font-bold">{{ sale.user?.name || 'Admin' }}</span></p>
            <p class="text-xl mt-2">Metode: <span class="font-bold">{{ sale.payment_method.toUpperCase() }}</span></p>
            <p class="text-2xl mt-4 text-green-600 font-bold">PAID</p>
          </div>
        </div>

        <!-- Tabel Produk -->
        <div class="bg-gray-50 rounded-2xl p-8">
          <table class="w-full text-left">
            <thead>
              <tr class="border-b-4 border-indigo-600">
                <th class="pb-4 text-xl font-bold text-indigo-700">Produk</th>
                <th class="pb-4 text-xl font-bold text-center text-indigo-700">Qty</th>
                <th class="pb-4 text-xl font-bold text-right text-indigo-700">Harga</th>
                <th class="pb-4 text-xl font-bold text-right text-indigo-700">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in sale.items" :key="item.id" class="border-b hover:bg-white transition">
                <td class="py-6">
                  <div class="text-xl font-semibold">{{ item.product.name }}</div>
                  <div class="text-gray-500">SKU: {{ item.product.sku }}</div>
                </td>
                <td class="py-6 text-center text-2xl font-bold">{{ item.quantity }}</td>
                <td class="py-6 text-right">Rp {{ $filters.formatRupiah(item.price) }}</td>
                <td class="py-6 text-right text-xl font-bold">Rp {{ $filters.formatRupiah(item.quantity * item.price) }}</td>
              </tr>
              <tr class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                <td colspan="3" class="py-8 text-right text-3xl font-bold pr-10">TOTAL BAYAR</td>
                <td class="py-8 text-right text-4xl font-bold">Rp {{ $filters.formatRupiah(sale.total) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- QR & Tanda Tangan -->
        <div class="flex justify-between items-end mt-16">
          <div class="text-center">
            <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent('https://franklin.app/invoice/' + sale.id)}`" 
                 class="mx-auto border-8 border-white rounded-2xl shadow-2xl">
            <p class="mt-4 text-gray-600 font-medium">Scan untuk verifikasi invoice</p>
          </div>
          <div class="text-right">
            <p class="text-gray-700">Hormat kami,</p>
            <div class="mt-10">
              <p class="text-2xl font-bold text-indigo-700">PT FRANKLIN INDONESIA</p>
              <p class="text-gray-600">Jakarta • Indonesia</p>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-indigo-800 to-purple-900 text-white text-center py-8 text-lg">
        Terima kasih atas kepercayaan Anda • Franklin Management System © 2025
      </div>
    </div>
  </div>
</template>

<style>
@media print {
  body { background: white; }
  .no-print { display: none; }
}
</style>