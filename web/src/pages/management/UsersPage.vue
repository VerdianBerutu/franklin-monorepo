<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Users Management</h1>
        <p class="text-gray-600 mt-1">Kelola pengguna sistem</p>
      </div>
      <button 
        @click="openAddModal"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center"
      >
        <i class="fas fa-plus mr-2"></i>
        Add User
      </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <!-- Filter & Search -->
      <div class="mb-6 flex gap-4">
        <div class="flex-1">
          <input 
            v-model="filters.search"
            type="text" 
            placeholder="Search users..." 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedSearch"
          />
        </div>
        <select 
          v-model="filters.role"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          @change="fetchUsers"
        >
          <option value="">All Roles</option>
          <option value="admin">Admin</option>
          <option value="staff">Staff</option>
          <option value="user">User</option>
        </select>
        <select 
          v-model="filters.per_page"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          @change="fetchUsers"
        >
          <option value="10">10 per page</option>
          <option value="25">25 per page</option>
          <option value="50">50 per page</option>
        </select>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
        <p class="text-gray-600">Loading users...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-12">
        <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-4"></i>
        <p class="text-red-600 font-medium mb-4">{{ error }}</p>
        <button 
          @click="fetchUsers"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Try Again
        </button>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <span class="text-blue-600 font-semibold">
                      {{ getInitials(user.name) }}
                    </span>
                  </div>
                  <div>
                    <div class="font-medium text-gray-900">{{ user.name }}</div>
                    <div class="text-sm text-gray-500">ID: {{ user.id }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ user.email }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  v-for="role in user.roles" 
                  :key="role"
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                  :class="getRoleBadgeClass(role)"
                >
                  {{ role }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                  Active
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button 
                  @click="openEditModal(user)"
                  class="text-blue-600 hover:text-blue-800 mr-3"
                  title="Edit"
                >
                  <i class="fas fa-edit"></i>
                </button>
                <button 
                  @click="confirmDelete(user)"
                  class="text-red-600 hover:text-red-800"
                  title="Delete"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Empty State -->
        <div v-if="users.length === 0" class="text-center py-12">
          <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
          <p class="text-gray-600">No users found</p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total > 0" class="mt-6 flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Showing <span class="font-medium">{{ pagination.from }}</span> to 
          <span class="font-medium">{{ pagination.to }}</span> of 
          <span class="font-medium">{{ pagination.total }}</span> results
        </div>
        <div class="flex gap-2">
          <button 
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Previous
          </button>
          <button 
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-3 py-1 rounded',
              page === pagination.current_page 
                ? 'bg-blue-600 text-white' 
                : 'border border-gray-300 hover:bg-gray-50'
            ]"
          >
            {{ page }}
          </button>
          <button 
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- User Modal (Add/Edit) -->
    <UserModal
      v-if="showModal"
      :user="selectedUser"
      @close="closeModal"
      @saved="handleSaved"
    />

    <!-- Delete Confirmation Modal -->
    <DeleteConfirmModal
      v-if="showDeleteModal"
      :user="userToDelete"
      @close="showDeleteModal = false"
      @confirmed="handleDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { userAPI } from '@/services/user'
import UserModal from '@/components/modals/UserModal.vue'
import DeleteConfirmModal from '@/components/modals/DeleteConfirmModal.vue'

// State
const users = ref([])
const loading = ref(false)
const error = ref(null)
const showModal = ref(false)
const showDeleteModal = ref(false)
const selectedUser = ref(null)
const userToDelete = ref(null)

// Filters
const filters = ref({
  search: '',
  role: '',
  per_page: 10,
  page: 1
})

// Pagination
const pagination = ref({
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0,
  total: 0
})

// Computed
const visiblePages = computed(() => {
  const pages = []
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  
  let start = Math.max(1, current - 2)
  let end = Math.min(last, current + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
const fetchUsers = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await userAPI.getUsers(filters.value)
    
    console.log('Users response:', response.data)
    
    const data = response.data.data
    
    users.value = data.data || []
    pagination.value = {
      current_page: data.current_page || 1,
      last_page: data.last_page || 1,
      from: data.from || 0,
      to: data.to || 0,
      total: data.total || 0
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to fetch users'
    console.error('Error fetching users:', err)
  } finally {
    loading.value = false
  }
}

// Debounce search
let searchTimeout
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    filters.value.page = 1
    fetchUsers()
  }, 500)
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    filters.value.page = page
    fetchUsers()
  }
}

const openAddModal = () => {
  selectedUser.value = null
  showModal.value = true
}

const openEditModal = (user) => {
  selectedUser.value = { ...user }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedUser.value = null
}

const handleSaved = () => {
  closeModal()
  fetchUsers()
}

const confirmDelete = (user) => {
  userToDelete.value = user
  showDeleteModal.value = true
}

const handleDelete = async () => {
  try {
    await userAPI.deleteUser(userToDelete.value.id)
    showDeleteModal.value = false
    userToDelete.value = null
    fetchUsers()
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete user')
  }
}

const getInitials = (name) => {
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const getRoleBadgeClass = (role) => {
  const classes = {
    admin: 'bg-purple-100 text-purple-800',
    staff: 'bg-blue-100 text-blue-800',
    user: 'bg-gray-100 text-gray-800'
  }
  return classes[role] || 'bg-gray-100 text-gray-800'
}

// Lifecycle
onMounted(() => {
  fetchUsers()
})
</script>