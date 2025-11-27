<!-- resources/js/components/customers/CustomersTable.vue -->
<template>
  <div class="bg-white shadow-sm rounded-lg overflow-hidden">
    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Name
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Company
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Email / Phone
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Type
            </th>
            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Action
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="c in customers" :key="c.id" class="hover:bg-gray-50 transition-colors">
            <!-- Name Column -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                  {{ getInitials(c.name) }}
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">{{ c.name }}</div>
                </div>
              </div>
            </td>

            <!-- Company Column -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-lg flex items-center justify-center">
                  <i class="fas fa-building text-blue-600 text-xs"></i>
                </div>
                <div class="ml-3">
                  <div class="text-sm text-gray-900">{{ c.company || '-' }}</div>
                </div>
              </div>
            </td>

            <!-- Email / Phone Column -->
            <td class="px-6 py-4 text-sm text-gray-600">
              <!-- Email -->
              <div class="flex items-center mb-1" v-if="c.email">
                <i class="fas fa-envelope text-gray-400 text-xs mr-2 w-4"></i>
                <span class="font-medium text-gray-700">{{ c.email }}</span>
              </div>
              <!-- Phone -->
              <div class="flex items-center" v-if="c.phone">
                <i class="fas fa-phone text-gray-400 text-xs mr-2 w-4"></i>
                <span class="text-gray-500">{{ c.phone }}</span>
              </div>
              <div v-if="!c.email && !c.phone" class="text-gray-400">-</div>
            </td>

            <!-- Type Column -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 mr-2" v-if="c.type === 'company'">
                  <i class="fas fa-building text-blue-500 text-sm"></i>
                </div>
                <div class="flex-shrink-0 mr-2" v-else>
                  <i class="fas fa-user text-green-500 text-sm"></i>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="c.type === 'company' 
                        ? 'bg-blue-100 text-blue-800' 
                        : 'bg-green-100 text-green-800'">
                  {{ c.type === 'company' ? 'Company' : 'Individual' }}
                </span>
              </div>
            </td>

            <!-- Action Column -->
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
              <button @click="$emit('edit', c)" 
                      class="text-indigo-600 hover:text-indigo-900 transition-colors p-2 rounded-lg hover:bg-indigo-50"
                      title="Edit Customer">
                <i class="fas fa-edit"></i>
              </button>
              <button @click="$emit('delete', c)" 
                      class="text-red-600 hover:text-red-900 transition-colors p-2 rounded-lg hover:bg-red-50"
                      title="Delete Customer">
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

    <!-- Pagination -->
    <div v-else class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
      <!-- Mobile -->
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

      <!-- Desktop -->
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            Showing
            <span class="font-medium">{{ (pagination.current_page - 1) * pagination.per_page + 1 }}</span>
            to
            <span class="font-medium">{{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}</span>
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
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
              <i class="fas fa-chevron-left"></i>
            </button>

            <!-- Page Numbers -->
            <template v-for="page in visiblePages" :key="page">
              <button v-if="page === '...'" disabled
                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                ...
              </button>
              <button v-else
                      @click="$emit('page-change', page)"
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

const props = defineProps({
  customers: {
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
      per_page: 10,
      total: 0
    })
  }
})

defineEmits(['edit', 'delete', 'page-change'])

const visiblePages = computed(() => {
  const total = props.pagination.last_page || 1
  const current = props.pagination.current_page || 1
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

// Function untuk mendapatkan inisial nama (logo)
const getInitials = (name) => {
  if (!name) return '?'
  return name
    .split(' ')
    .map(word => word.charAt(0).toUpperCase())
    .join('')
    .substring(0, 2)
}
</script>