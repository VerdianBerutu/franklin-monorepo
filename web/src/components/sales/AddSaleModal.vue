<template>
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
              <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
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
                  <th class="px-4 py-3 w-24">Qty</th>
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
                      <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                  </td>
                  <td class="px-4 py-3">
                    <input type="number" v-model.number="item.qty" min="1" required class="w-full px-3 py-2 border rounded text-center">
                  </td>
                  <td class="px-4 py-3 text-right font-medium">
                    Rp {{ formatRupiah(item.price) }}
                  </td>
                  <td class="px-4 py-3 text-right font-bold">
                    Rp {{ formatRupiah(item.qty * item.price) }}
                  </td>
                  <td class="px-4 py-3 text-center">
                    <button @click="removeItem(index)" class="text-red-600 hover:text-red-800">Remove</button>
                  </td>
                </tr>
                <tr>
                  <td colspan="5" class="px-4 py-4 text-left">
                    <button @click="addItem" type="button" class="text-blue-600 font-medium hover:text-blue-800">
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
          <button @click="$emit('close')" type="button" class="px-8 py-3 border rounded-lg hover:bg-gray-100">Batal</button>
          <button @click="submit" :disabled="!canSubmit" type="button" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400">
            <i class="fas fa-save mr-2"></i>
            {{ submitting ? 'Menyimpan...' : 'Simpan Penjualan' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
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
      return this.form.items.reduce((sum, item) => sum + (item.qty * item.price), 0)
    },
    grandTotal() {
      return this.subtotal + (this.form.tax || 0) - (this.form.discount || 0)
    },
    canSubmit() {
      return !this.submitting && 
             this.form.customer_id && 
             this.form.items.length > 0 && 
             this.form.items.every(i => i.product_id && i.qty > 0 && i.price > 0)
    }
  },
  methods: {
    addItem() {
      this.form.items.push({ 
        product_id: '', 
        qty: 1,  // ✅ Konsisten pakai 'qty'
        price: 0 
      })
    },
    removeItem(index) {
      this.form.items.splice(index, 1)
    },
    updatePrice(index) {
      const product = this.products.find(p => p.id === this.form.items[index].product_id)
      if (product) {
        this.form.items[index].price = parseFloat(product.price) || 0
      }
    },
    async submit() {
      if (!this.canSubmit) {
        alert('Mohon lengkapi semua field yang diperlukan')
        return
      }

      this.submitting = true
      
      try {
        // ✅ Data yang dikirim sudah sesuai dengan yang backend expect
        const submitData = {
          customer_id: this.form.customer_id,
          sale_date: this.form.sale_date,
          items: this.form.items.map(item => ({
            product_id: item.product_id,
            qty: item.qty,  // ✅ Backend expect 'qty'
            price: parseFloat(item.price) || 0
          })),
          tax: parseFloat(this.form.tax) || 0,
          discount: parseFloat(this.form.discount) || 0,
          payment_method: this.form.payment_method,
          notes: this.form.notes || null
        }
        
        console.log('🚀 Submitting sale data:', submitData)
        
        const response = await saleService.create(submitData)
        
        console.log('✅ Sale created successfully:', response.data)
        
        alert('Penjualan berhasil disimpan!')
        
        // ✅ Emit 'saved' agar parent component refresh data
        this.$emit('saved')
        this.$emit('close')
        this.resetForm()
        
      } catch (error) {
        console.error('❌ Error saving sale:', error)
        console.error('Error response:', error.response?.data)
        
        const errorMessage = error.response?.data?.message || error.message || 'Terjadi kesalahan'
        alert('Gagal menyimpan penjualan: ' + errorMessage)
      } finally {
        this.submitting = false
      }
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
  watch: {
    isOpen(val) {
      if (val && this.form.items.length === 0) {
        this.addItem()
      }
    }
  },
  mounted() {
    this.addItem()
  }
}
</script>