<!-- web/src/components/customers/CustomersTable.vue -->
<template>
  <div class="bg-white shadow-sm rounded-lg overflow-hidden">
    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email / Phone</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="c in customers" :key="c.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ c.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ c.company || '-' }}</td>
            <td class="px-6 py-4 text-sm text-gray-600">
              <div class="font-medium">{{ c.email || '-' }}</div>
              <div class="text-gray-500 text-xs">{{ c.phone || '-' }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="c.type === 'company' 
                  ? 'bg-blue-100 text-blue-800' 
                  : 'bg-green-100 text-green-800'">
                {{ c.type === 'company' ? 'Company' : 'Individual' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-4">
              <button @click="$emit('edit', c)" class="text-indigo-600 hover:text-indigo-900">
                <i class="fas fa-edit"></i>
              </button>
              <!-- DI SINI YANG SALAH: class="="text-red-600 → dihapus tanda "=" -->
              <button @click="$emit('delete', c)" class="text-red-600 hover:text-red-900">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading && customers.length === 0" class="text-center py-16 text-gray-500">
      <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
      <p class="text-lg">Belum ada customer</p>
      <p class="text-sm">Klik tombol "Add Customer" untuk menambahkan pelanggan pertama</p>
    </div>

    <!-- Pagination - Flowbite Style (100% SAMA KAYAK SALES) -->
    <div v-else class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
      <div class="flex-1 flex justify-between sm:hidden">
        <button @click="$emit('page-change', pagination.current_page - 1)" 
                :disabled="pagination.current_page <= 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">
          Previous
        </button>
        <button @click="$emit('page-change', pagination.current_page + 1)" 
                :disabled="pagination.current_page >= pagination.last_page"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">
          Next
        </button>
      </div>

      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            Showing 
            <span class="font-medium">{{ (pagination.current_page - 1) * 10 + 1 }}</span> 
            to 
            <span class="font-medium">{{ Math.min(pagination.current_page * 10, pagination.total) }}</span> 
            of 
            <span class="font-medium">{{ pagination.total }}</span> results
          </p>
        </div>
        <div>
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <!-- Previous -->
            <button @click="$emit('page-change', pagination.current_page - 1)"
                    :disabled="pagination.current_page <= 1"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
              <i class="fas fa-chevron-left"></i>
            </button>

            <!-- Page Numbers -->
            <template v-for="page in visiblePages" :key="page">
              <button v-if="page === '...'" disabled
                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                ...
              </button>
              <button v-else @click="$emit('page-change', page)"
                      :class="page === pagination.current_page 
                        ? 'z-10 bg-blue-600 border-blue-600 text-white' 
                        : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'"
                      class="relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                {{ page }}
              </button>
            </template>

            <!-- Next -->
            <button @click="$emit('page-change', pagination.current_page + 1)"
                    :disabled="pagination.current_page >= pagination.last_page"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
              <i class="fas fa-chevron-right"></i>
            </button>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

defineProps({
  customers: Array,
  loading: Boolean,
  pagination: Object
})

defineEmits(['edit', 'delete', 'page-change'])

// Smart pagination – max 7 tombol + "..." kalau banyak halaman
const visiblePages = computed(() => {
  const total = pagination.value?.last_page || 1
  const current = pagination.value?.current_page || 1
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