<!-- web/src/components/customers/CustomersTable.vue -->
<template>
  <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <!-- NAME: 20% -->
            <th scope="col" class="w-[20%] px-6 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
              Name
            </th>
            
            <!-- COMPANY: 18% -->
            <th scope="col" class="w-[18%] px-6 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
              Company
            </th>
            
            <!-- EMAIL/PHONE: 32% (Diperbesar) -->
            <th scope="col" class="w-[32%] px-6 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
              Email / Phone
            </th>
            
            <!-- TYPE: 15% -->
            <th scope="col" class="w-[15%] px-6 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
              Type
            </th>
            
            <!-- ACTION: 15% -->
            <th scope="col" class="w-[15%] px-6 py-3.5 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
              Action
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="c in customers" :key="c.id" class="hover:bg-blue-50 transition-colors duration-150">
            <!-- Name -->
            <td class="px-6 py-4 text-sm font-medium text-gray-900">
              {{ c.name }}
            </td>
            
            <!-- Company -->
            <td class="px-6 py-4 text-sm text-gray-600">
              <span class="truncate block">{{ c.company || '-' }}</span>
            </td>
            
            <!-- Email / Phone -->
            <td class="px-6 py-4 text-sm">
              <div class="space-y-1">
                <div class="font-medium text-gray-900 truncate">
                  {{ c.email || '-' }}
                </div>
                <div class="text-gray-500 text-xs">
                  <i class="fas fa-phone text-gray-400 mr-1"></i>
                  {{ c.phone || '-' }}
                </div>
              </div>
            </td>
            
            <!-- Type -->
            <td class="px-6 py-4">
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                :class="c.type === 'company' 
                  ? 'bg-blue-100 text-blue-800' 
                  : 'bg-green-100 text-green-800'">
                <i :class="c.type === 'company' ? 'fas fa-building' : 'fas fa-user'" class="mr-1.5 text-[10px]"></i>
                {{ c.type === 'company' ? 'Company' : 'Individual' }}
              </span>
            </td>
            
            <!-- Actions -->
            <td class="px-6 py-4 text-right">
              <div class="flex items-center justify-end gap-3">
                <button @click="$emit('edit', c)" 
                        class="text-indigo-600 hover:text-indigo-900 transition-colors"
                        title="Edit">
                  <i class="fas fa-edit text-base"></i>
                </button>
                <button @click="$emit('delete', c)" 
                        class="text-red-600 hover:text-red-900 transition-colors"
                        title="Delete">
                  <i class="fas fa-trash text-base"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-16">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
      <p class="text-gray-500 text-sm">Loading customers...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading && customers.length === 0" class="text-center py-16 px-4">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
        <i class="fas fa-users text-3xl text-gray-400"></i>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada customer</h3>
      <p class="text-sm text-gray-500 mb-6">Klik tombol "Add Customer" untuk menambahkan pelanggan pertama</p>
    </div>

    <!-- Pagination -->
    <div v-else class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
      <!-- Mobile Pagination -->
      <div class="flex-1 flex justify-between sm:hidden">
        <button @click="$emit('page-change', pagination.current_page - 1)" 
                :disabled="pagination.current_page <= 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
          Previous
        </button>
        <button @click="$emit('page-change', pagination.current_page + 1)" 
                :disabled="pagination.current_page >= pagination.last_page"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
          Next
        </button>
      </div>

      <!-- Desktop Pagination -->
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            Showing 
            <span class="font-medium">{{ (pagination.current_page - 1) * 10 + 1 }}</span> 
            to 
            <span class="font-medium">{{ Math.min(pagination.current_page * 10, pagination.total) }}</span> 
            of 
            <span class="font-medium">{{ pagination.total }}</span> 
            results
          </p>
        </div>
        <div>
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <!-- Previous -->
            <button @click="$emit('page-change', pagination.current_page - 1)"
                    :disabled="pagination.current_page <= 1"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <span class="sr-only">Previous</span>
              <i class="fas fa-chevron-left text-xs"></i>
            </button>

            <!-- Page Numbers -->
            <template v-for="page in visiblePages" :key="page">
              <button v-if="page === '...'" disabled
                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 cursor-default">
                ...
              </button>
              <button v-else @click="$emit('page-change', page)"
                      :class="page === pagination.current_page 
                        ? 'z-10 bg-blue-600 border-blue-600 text-white hover:bg-blue-700' 
                        : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'"
                      class="relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors">
                {{ page }}
              </button>
            </template>

            <!-- Next -->
            <button @click="$emit('page-change', pagination.current_page + 1)"
                    :disabled="pagination.current_page >= pagination.last_page"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              <span class="sr-only">Next</span>
              <i class="fas fa-chevron-right text-xs"></i>
            </button>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  customers: Array,
  loading: Boolean,
  pagination: Object
})

defineEmits(['edit', 'delete', 'page-change'])

const visiblePages = computed(() => {
  const total = props.pagination?.last_page || 1
  const current = props.pagination?.current_page || 1
  const pages = []

  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i)
  } else {
    if (current <= 4) {
      pages.push(1, 2, 3, 4, 5, '...', total)
    } else if (current >= total - 3) {
      pages.push(1, '...', total - 4, total - 3, total - 2, total - 1, total)
    } else {
      pages.push(1, '...', current - 1, current, current + 1, '...', total)
    }
  }
  return pages
})
</script>