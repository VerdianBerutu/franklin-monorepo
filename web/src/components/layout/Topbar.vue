<template>
  <header class="topbar bg-white border-b border-gray-200 sticky top-0 z-40">
    <div class="flex items-center justify-between px-6 py-4">
      <!-- Page Title - KOSONGKAN -->
      <div></div>

      <!-- Topbar Actions -->
      <div class="flex items-center space-x-4">
        <!-- Notification Bell -->
        <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
          <i class="fas fa-bell"></i>
          <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>

        <!-- User Profile Dropdown -->
        <div class="relative" ref="dropdown">
          <button 
            @click="toggleDropdown"
            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
              <span class="text-white text-sm font-bold">A</span>
            </div>
          </button>

          <!-- Dropdown Menu -->
          <div 
            v-if="isDropdownOpen"
            class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
          >
            <!-- User Info -->
            <div class="px-4 py-3 border-b border-gray-100">
              <p class="font-semibold text-gray-900">{{ userName }}</p>
              <p class="text-sm text-gray-600">{{ userEmail }}</p>
            </div>

            <!-- Menu Items -->
            <router-link 
           to="/profile" 
           @click="isDropdownOpen = false"
            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
            >
             <i class="fas fa-key w-5 text-gray-400"></i>
              <span class="ml-3">Ganti Password</span>
                </router-link>

            <!-- Sign Out -->
            <div class="border-t border-gray-100 pt-1">
              <button 
                @click="handleSignOut"
                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
              >
                <i class="fas fa-sign-out-alt w-5"></i>
                <span class="ml-3">Sign out</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const isDropdownOpen = ref(false)
const dropdown = ref(null)

// Computed properties untuk user data
const userName = computed(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  return user.name || 'Admin User'
})

const userEmail = computed(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  return user.email || 'admin@franklin.com'
})

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
}

const closeDropdown = (event) => {
  if (dropdown.value && !dropdown.value.contains(event.target)) {
    isDropdownOpen.value = false
  }
}

const handleSignOut = () => {
  // Tampilkan konfirmasi
  if (confirm('Apakah Anda yakin ingin keluar?')) {
    // Hapus semua data auth dari localStorage
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
    localStorage.removeItem('token')
    
    // Hapus header authorization dari axios jika ada
    if (window.axios && window.axios.defaults.headers.common['Authorization']) {
      delete window.axios.defaults.headers.common['Authorization']
    }
    
    // Tutup dropdown
    isDropdownOpen.value = false
    
    // Redirect ke halaman login
    router.push('/login')
    
    // Optional: Force reload untuk reset state aplikasi
    setTimeout(() => {
      window.location.reload()
    }, 100)
  }
}

onMounted(() => {
  document.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown)
})
</script>