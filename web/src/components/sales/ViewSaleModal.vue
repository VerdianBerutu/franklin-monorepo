<template>
  <Teleport to="body">
    <div 
      v-if="isOpen" 
      class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
      @click.self="$emit('close')">
      <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Sale Details</h2>
            <p class="text-sm text-gray-500 mt-1">Invoice: {{ sale?.invoice_number }}</p>
          </div>
          <button 
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-times text-xl"></i>
          </button>
        </div>

        <!-- Content (sama seperti punya kamu, aku biarkan utuh) -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
          <!-- Status Badge -->
          <div class="mb-6">
            <span 
              class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium"
              :class="getStatusClass(sale?.payment_status)">
              <i class="fas fa-circle text-[6px] mr-2"></i>
              {{ formatStatus(sale?.payment_status) }}
            </span>
          </div>

          <!-- Sale Info Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Customer Info -->
            <div class="space-y-4 p-4 bg-gray-50 rounded-lg">
              <h3 class="font-semibold text-gray-900 text-lg mb-3 flex items-center gap-2">
                <i class="fas fa-user text-blue-600"></i>
                Customer Information
              </h3>
              <div><label class="text-xs text-gray-500 uppercase font-medium">Customer Name</label>
                <p class="text-gray-900 font-medium">{{ sale?.customer_name || sale?.customer?.name || '-' }}</p>
              </div>
              <div><label class="text-xs text-gray-500 uppercase font-medium">Email</label>
                <p class="text-gray-900">{{ sale?.customer?.email || '-' }}</p>
              </div>
              <div><label class="text-xs text-gray-500 uppercase font-medium">Phone</label>
                <p class="text-gray-900">{{ sale?.customer?.phone || '-' }}</p>
              </div>
              <div><label class="text-xs text-gray-500 uppercase font-medium">Company</label>
                <p class="text-gray-900">{{ sale?.customer?.company || '-' }}</p>
              </div>
            </div>

            <!-- Sale Info -->
            <div class="space-y-4 p-4 bg-blue-50 rounded-lg">
              <h3 class="font-semibold text-gray-900 text-lg mb-3 flex items-center gap-2">
                <i class="fas fa-file-invoice text-blue-600"></i>
                Sale Information
              </h3>
              <div><label class="text-xs text-gray-500 uppercase font-medium">Invoice Number</label>
                <p class="text-gray-900 font-mono font-medium">{{ sale?.invoice_number }}</p>
              </div>
              <div><label class="text-xs text-gray-500 uppercase font-medium">Sale Date</label>
                <p class="text-gray-900">{{ formatDate(sale?.sale_date) }}</p>
              </div>
              <div><label class="text-xs text-gray-500 uppercase font-medium">Payment Method</label>
                <p class="text-gray-900 capitalize">{{ sale?.payment_method || '-' }}</p>
              </div>
            </div>
          </div>

          <!-- Products Table (sama seperti punya kamu) -->
          <div class="mb-6">
            <h3 class="font-semibold text-gray-900 text-lg mb-3 flex items-center gap-2">
              <i class="fas fa-shopping-cart text-blue-600"></i>
              Products
            </h3>
            <div class="border border-gray-200 rounded-lg overflow-hidden">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="item in sale?.items" :key="item.id">
                    <td class="px-4 py-3 text-sm text-gray-900">
                      <div class="font-medium">{{ item.product_name || item.product?.name }}</div>
                      <div v-if="item.sku || item.product?.sku" class="text-gray-500 text-xs">
                        SKU: {{ item.sku || item.product?.sku }}
                      </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">{{ item.quantity }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ formatCurrency(item.price) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">
                      {{ formatCurrency(item.quantity * item.price) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Totals -->
          <div class="border-t border-gray-200 pt-4 bg-gray-50 rounded-lg p-4">
            <div class="flex justify-end">
              <div class="w-80 space-y-2">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Subtotal:</span>
                  <span class="text-gray-900 font-medium">{{ formatCurrency(sale?.subtotal) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Discount:</span>
                  <span class="text-red-600 font-medium">-{{ formatCurrency(sale?.discount) }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-3 mt-2">
                  <span class="text-gray-900">Total:</span>
                  <span class="text-blue-600">{{ formatCurrency(sale?.total) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50">
          <!-- TOMBOL PRINT INVOICE YANG BARU (INI YANG BIKIN GILA KEREN) -->
          <button 
            @click="printInvoice(sale)"
            class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all flex items-center gap-3 font-bold shadow-lg">
            <i class="fas fa-print text-xl"></i>
            PRINT INVOICE 
          </button>

          <button 
            @click="$emit('edit', sale)"
            class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors flex items-center gap-2 font-medium">
            <i class="fas fa-edit"></i>
            Edit Sale
          </button>
          <button 
            @click="$emit('close')"
            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
            Close
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
defineProps({
  isOpen: Boolean,
  sale: Object
})

defineEmits(['close', 'edit'])

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatCurrency = (amount) => {
  if (!amount && amount !== 0) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount)
}

const formatStatus = (status) => {
  if (!status) return 'Unknown'
  return status.charAt(0).toUpperCase() + status.slice(1).toLowerCase()
}

const getStatusClass = (status) => {
  const statusClasses = {
    'paid': 'bg-green-100 text-green-800',
    'pending': 'bg-yellow-100 text-yellow-800',
    'cancelled': 'bg-red-100 text-red-800',
    'draft': 'bg-gray-100 text-gray-800',
    'overdue': 'bg-orange-100 text-orange-800'
  }
  return statusClasses[status?.toLowerCase()] || 'bg-gray-100 text-gray-800'
}

// INI FUNGSI BARU — GANTI YANG LAMA!
const printInvoice = (sale) => {
  if (!sale) return

  const saleData = encodeURIComponent(JSON.stringify(sale))
  const printWindow = window.open(
    '/web/print-invoice?data=' + saleData,
    '_blank',
    'width=1100,height=800,scrollbars=yes,resizable=yes'
  )

  if (!printWindow) {
    alert('Izinkan popup untuk print invoice cakep!')
  }
}
</script>