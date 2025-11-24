<template>
  <!-- TEMPLATE TIDAK BERUBAH (tetap sama seperti punya Anda) -->
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl m-4 max-h-screen overflow-y-auto">
      <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center z-10">
        <h2 class="text-2xl font-bold">Tambah Penjualan Baru</h2>
        <button @click="$emit('close')" class="text-gray-500 hover:text-red-600">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <div class="p-6 space-y-6">
        <!-- Customer & Tanggal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium mb-2">Customer <span class="text-red-500">*</span></label>
            <select v-model="form.customer_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
              <option value="">Pilih Customer</option>
              <option v-for="c in customers" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Tanggal Penjualan <span class="text-red-500">*</span></label>
            <input type="date" v-model="form.sale_date" required class="w-full px-4 py-2 border rounded-lg">
          </div>
        </div>

        <!-- Daftar Produk -->
        <div>
          <h3 class="text-lg font-semibold mb-3">Produk yang Dijual</h3>
          <div class="border rounded-lg overflow-hidden">
            <table class="w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left">Produk</th>
                  <th class="px-4 py-3 w-24">quantity</th>
                  <th class="px-4 py-3 w-32 text-right">Harga</th>
                  <th class="px-4 py-3 w-32 text-right">Subtotal</th>
                  <th class="px-4 py-3 w-20"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in form.items" :key="index" class="border-t">
                  <td class="px-4 py-3">
                    <select v-model="item.product_id" @change="updatePrice(index)" required class="w-full px-3 py-2 border rounded">
                      <option value="">Pilih Produk</option>
                      <option v-for="p in products" :value="p.id">{{ p.name }}</option>
                    </select>
                  </td>
                  <td class="px-4 py-3">
                    <input type="number" v-model.number="item.quantity" min="1" required class="w-full px-3 py-2 border rounded text-center">
                  </td>
                  <td class="px-4 py-3 text-right font-medium">
                    Rp {{ formatRupiah(item.price) }}
                  </td>
                  <td class="px-4 py-3 text-right font-bold">
                    Rp {{ formatRupiah(item.quantity * item.price) }}
                  </td>
                  <td class="px-4 py-3 text-center">
                    <button @click="removeItem(index)" class="text-red-600 hover:text-red-800">Remove</button>
                  </td>
                </tr>
                <tr>
                  <td colspan="5" class="px-4 py-4 text-left">
                    <button @click="addItem" class="text-blue-600 font-medium hover:text-blue-800">
                      + Tambah Produk
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Total & Pajak/Diskon -->
        <div class="bg-gray-50 rounded-lg p-6 space-y-4">
          <div class="flex justify-between text-lg">
            <span>Subtotal</span>
            <span class="font-bold">Rp {{ formatRupiah(subtotal) }}</span>
          </div>
          <div class="flex justify-between">
            <span>Pajak (Tax)</span>
            <input type="number" v-model.number="form.tax" class="w-32 text-right px-3 py-1 border rounded" placeholder="0">
          </div>
          <div class="flex justify-between">
            <span>Diskon</span>
            <input type="number" v-model.number="form.discount" class="w-32 text-right px-3 py-1 border rounded" placeholder="0">
          </div>
          <div class="flex justify-between text-2xl font-bold text-blue-600 border-t pt-4">
            <span>TOTAL</span>
            <span>Rp {{ formatRupiah(grandTotal) }}</span>
          </div>
        </div>

        <!-- Payment Method & Catatan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium mb-2">Metode Pembayaran <span class="text-red-500">*</span></label>
            <select v-model="form.payment_method" required class="w-full px-4 py-2 border rounded-lg">
              <option value="cash">Cash</option>
              <option value="transfer">Transfer Bank</option>
              <option value="card">Kartu Kredit/Debit</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Catatan (Opsional)</label>
            <textarea v-model="form.notes" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea>
          </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex justify-end gap-4 pt-6 border-t">
          <button @click="$emit('close')" class="px-8 py-3 border rounded-lg hover:bg-gray-100">Batal</button>
          <button @click="submit" :disabled="!canSubmit" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400">
            <i class="fas fa-save mr-2"></i>
            Simpan Penjualan
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// FIX: Gunakan saleService yang sudah diimport
import { saleService } from '@/services/sale'

export default {
  props: ['isOpen', 'customers', 'products'],
  emits: ['close', 'saved'],
  data() {
    return {
      form: {
        customer_id: '',
        sale_date: new Date().toISOString().split('T')[0],
        items: [],
        tax: 0,
        discount: 0,
        payment_method: 'cash',
        notes: ''
      },
      submitting: false
    }
  },
  computed: {
    subtotal() {
      return this.form.items.reduce((sum, item) => sum + (item.quantity * item.price), 0)
    },
    grandTotal() {
      return this.subtotal + this.form.tax - this.form.discount
    },
    canSubmit() {
      return !this.submitting && 
             this.form.customer_id && 
             this.form.items.length > 0 && 
             this.form.items.every(i => i.product_id && i.quantity > 0)
    }
  },
  methods: {
    addItem() {
      this.form.items.push({ 
        product_id: '', 
        product_name: '', // 🔥 TAMBAH: product_name
        qty: 1, 
        price: 0 
      })
    },
    removeItem(index) {
      this.form.items.splice(index, 1)
    },
    updatePrice(index) {
      const product = this.products.find(p => p.id === this.form.items[index].product_id)
      if (product) {
        this.form.items[index].price = product.price
        this.form.items[index].product_name = product.name // 🔥 TAMBAH: set product_name
      }
    },
    async submit() {
      if (!this.canSubmit) return

      this.submitting = true
      
      try {
        // 🔥 FIX: Pastikan semua item punya product_name sebelum dikirim
        const submitData = {
          ...this.form,
          items: this.form.items.map(item => ({
            ...item,
            product_name: item.product_name || this.getProductName(item.product_id)
          }))
        }
        
        console.log('🚀 Creating sale with data:', submitData)
        
        // FIX: Gunakan saleService bukan axios langsung
        const response = await saleService.create(submitData)
        
        console.log('✅ Sale created successfully:', response.data)
        this.$emit('saved')
        this.$emit('close')
        this.resetForm()
        alert('Penjualan berhasil disimpan!')
        
      } catch (error) {
        console.error('❌ Error saving sale:', error)
        console.error('Error details:', error.response?.data)
        
        alert('Gagal menyimpan: ' + (error.response?.data?.message || error.message))
      } finally {
        this.submitting = false
      }
    },
    // 🔥 TAMBAH: Helper method untuk get product name
    getProductName(productId) {
      const product = this.products.find(p => p.id === productId)
      return product ? product.name : ''
    },
    resetForm() {
      this.form = {
        customer_id: '',
        sale_date: new Date().toISOString().split('T')[0],
        items: [],
        tax: 0,
        discount: 0,
        payment_method: 'cash',
        notes: ''
      }
      this.addItem()
    },
    formatRupiah(angka) {
      return new Intl.NumberFormat('id-ID').format(angka || 0)
    }
  },
  created() {
    this.addItem()
  }
}
</script>