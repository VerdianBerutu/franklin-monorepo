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
            <h2 class="text-2xl font-bold text-gray-900">Edit Sale</h2>
            <p class="text-sm text-gray-500 mt-1">Update sale information</p>
          </div>
          <button 
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-times text-xl"></i>
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
          <!-- Customer & Date -->
          <div class="grid grid-cols-2 gap-4 mb-6">
            <!-- Customer -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Customer <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.customer_id"
                required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Select Customer</option>
                <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                  {{ customer.name }}
                </option>
              </select>
            </div>

            <!-- Sale Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Sale Date <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.sale_date"
                type="date"
                required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
          </div>

          <!-- Payment Info -->
          <div class="grid grid-cols-2 gap-4 mb-6">
            <!-- Payment Method -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Payment Method <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.payment_method"
                required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Select Method</option>
                <option value="cash">Cash</option>
                <option value="transfer">Bank Transfer</option>
                <option value="credit_card">Credit Card</option>
                <option value="check">Check</option>
              </select>
            </div>

            <!-- Payment Status -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Payment Status <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.payment_status"
                required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>

          <!-- Products -->
          <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
              <label class="block text-sm font-medium text-gray-700">
                Products <span class="text-red-500">*</span>
              </label>
              <button 
                type="button"
                @click="addProduct"
                class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
                <i class="fas fa-plus"></i>
                Add Product
              </button>
            </div>

            <div class="space-y-3">
              <div v-for="(item, index) in form.items" :key="index" 
                   class="flex gap-3 items-start p-4 bg-gray-50 rounded-lg">
                <!-- Product -->
                <div class="flex-1">
                  <select 
                    v-model="item.product_id"
                    @change="updateProductPrice(index)"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Product</option>
                    <option v-for="product in products" :key="product.id" :value="product.id">
                      {{ product.name }} - {{ formatCurrency(product.price) }}
                    </option>
                  </select>
                </div>

                <!-- Quantity -->
                <div class="w-24">
                  <input 
                    v-model.number="item.quantity"
                    type="number"
                    min="1"
                    required
                    placeholder="Qty"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Price -->
                <div class="w-32">
                  <input 
                    v-model.number="item.price"
                    type="number"
                    min="0"
                    required
                    placeholder="Price"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Subtotal (read-only) -->
                <div class="w-32">
                  <input 
                    :value="formatCurrency(item.quantity * item.price)"
                    readonly
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-100 text-gray-700">
                </div>

                <!-- Remove -->
                <button 
                  type="button"
                  @click="removeProduct(index)"
                  :disabled="form.items.length === 1"
                  class="text-red-600 hover:text-red-800 disabled:opacity-30 disabled:cursor-not-allowed mt-2">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Tax & Discount -->
          <div class="grid grid-cols-2 gap-4 mb-6">
            <!-- Tax Rate -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Tax Rate (%)
              </label>
              <input 
                v-model.number="form.tax_rate"
                type="number"
                min="0"
                max="100"
                step="0.01"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Discount -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Discount (Rp)
              </label>
              <input 
                v-model.number="form.discount"
                type="number"
                min="0"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
          </div>

          <!-- Notes -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Notes
            </label>
            <textarea 
              v-model="form.notes"
              rows="3"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Additional notes..."></textarea>
          </div>

          <!-- Totals Summary -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal:</span>
                <span class="font-medium">{{ formatCurrency(subtotal) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Tax ({{ form.tax_rate }}%):</span>
                <span class="font-medium">{{ formatCurrency(taxAmount) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Discount:</span>
                <span class="font-medium text-red-600">-{{ formatCurrency(form.discount) }}</span>
              </div>
              <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2">
                <span>Total:</span>
                <span class="text-blue-600">{{ formatCurrency(total) }}</span>
              </div>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-600">{{ error }}</p>
          </div>
        </form>

        <!-- Footer Actions -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50">
          <button 
            type="button"
            @click="$emit('close')"
            class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
            Cancel
          </button>
          <button 
            type="submit"
            @click="handleSubmit"
            :disabled="saving"
            class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium">
            {{ saving ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { saleService } from '@/services/sale'

const props = defineProps({
  isOpen: Boolean,
  sale: Object,
  customers: Array,
  products: Array
})

const emit = defineEmits(['close', 'saved'])

const form = ref({
  customer_id: '',
  sale_date: '',
  payment_method: '',
  payment_status: '',
  tax_rate: 0,
  discount: 0,
  notes: '',
  items: []
})

const saving = ref(false)
const error = ref('')

// Watch sale prop to populate form
watch(() => props.sale, (newSale) => {
  if (newSale && props.isOpen) {
    form.value = {
      customer_id: newSale.customer_id || '',
      sale_date: newSale.sale_date || '',
      payment_method: newSale.payment_method || '',
      payment_status: newSale.payment_status || 'pending',
      tax_rate: newSale.tax_rate || 0,
      discount: newSale.discount || 0,
      notes: newSale.notes || '',
      items: newSale.items?.map(item => ({
        product_id: item.product_id,
        quantity: item.quantity,
        price: item.price
      })) || []
    }
  }
}, { immediate: true })

// Computed
const subtotal = computed(() => {
  return form.value.items.reduce((sum, item) => {
    return sum + (item.quantity * item.price)
  }, 0)
})

const taxAmount = computed(() => {
  return subtotal.value * (form.value.tax_rate / 100)
})

const total = computed(() => {
  return subtotal.value + taxAmount.value - form.value.discount
})

// Methods
const addProduct = () => {
  form.value.items.push({
    product_id: '',
    quantity: 1,
    price: 0
  })
}

const removeProduct = (index) => {
  if (form.value.items.length > 1) {
    form.value.items.splice(index, 1)
  }
}

const updateProductPrice = (index) => {
  const item = form.value.items[index]
  const product = props.products.find(p => p.id === item.product_id)
  if (product) {
    item.price = product.price
  }
}

const handleSubmit = async () => {
  saving.value = true
  error.value = ''

  try {
    const data = {
      ...form.value,
      subtotal: subtotal.value,
      tax_amount: taxAmount.value,
      total: total.value
    }

    await saleService.update(props.sale.id, data)
    
    emit('saved')
    emit('close')
  } catch (err) {
    console.error('Update error:', err)
    error.value = err.response?.data?.message || 'Failed to update sale'
  } finally {
    saving.value = false
  }
}

const formatCurrency = (value) => {
  if (!value && value !== 0) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value)
}
</script>