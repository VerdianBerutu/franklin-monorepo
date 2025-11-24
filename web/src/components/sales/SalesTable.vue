<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
      <p class="text-gray-600">Loading sales...</p>
    </div>

    <!-- Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              ID
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Invoice
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Customer
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Product
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Amount
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Status
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Action
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="sale in sales" :key="sale.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ sale.id }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ sale.invoice_number }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ sale.customer_name || sale.customer?.name || '-' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ getProductNames(sale) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ formatCurrency(sale.total || sale.amount) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="['px-2 py-1 text-xs font-semibold rounded-full', getStatusClass(sale.status)]">
                {{ formatStatus(sale.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <div class="flex items-center gap-2">
                <button
                  @click="$emit('view', sale)"
                  class="text-blue-600 hover:text-blue-800"
                  title="View"
                >
                  <i class="fas fa-eye"></i>
                </button>
                <button
                  @click="$emit('edit', sale)"
                  class="text-green-600 hover:text-green-800"
                  title="Edit"
                >
                  <i class="fas fa-edit"></i>
                </button>
                <button
                  @click="$emit('print', sale)"
                  class="text-purple-600 hover:text-purple-800"
                  title="Print"
                >
                  <i class="fas fa-print"></i>
                </button>
                <button
                  @click="$emit('delete', sale)"
                  class="text-red-600 hover:text-red-800"
                  title="Delete"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty State -->
      <div v-if="sales.length === 0" class="text-center py-12">
        <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
        <p class="text-gray-600">No sales found</p>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="sales.length > 0" class="flex justify-between items-center px-6 py-4 border-t border-gray-200">
      <div class="text-sm text-gray-600">
        Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }}
      </div>
      <div class="flex gap-2">
        <button
          v-for="page in visiblePages"
          :key="page"
          @click="$emit('page-change', page)"
          :class="[
            'px-3 py-1 rounded',
            page === pagination.current_page
              ? 'bg-blue-600 text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          {{ page }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { saleService } from '@/services/sale'

const props = defineProps({
  sales: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  pagination: {
    type: Object,
    default: () => ({
      current_page: 1,
      last_page: 1,
      from: 0,
      to: 0,
      total: 0
    })
  }
})

defineEmits(['edit', 'delete', 'view', 'print', 'page-change'])

const visiblePages = computed(() => {
  const pages = []
  const current = props.pagination.current_page
  const last = props.pagination.last_page
  
  let start = Math.max(1, current - 2)
  let end = Math.min(last, current + 2)

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

const formatCurrency = (value) => {
  return saleService.formatCurrency(value)
}

const getStatusClass = (status) => {
  return saleService.getStatusClass(status)
}

const formatStatus = (status) => {
  return saleService.formatStatus(status)
}

const getProductNames = (sale) => {
  if (sale.items && Array.isArray(sale.items) && sale.items.length > 0) {
    return sale.items.map(item => item.product_name || item.product?.name).join(', ')
  }
  return sale.product_name || sale.product?.name || '-'
}
</script>