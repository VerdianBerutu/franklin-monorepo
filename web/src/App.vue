<template>
  <MainLayout v-if="isAuthenticated">
    <router-view />
  </MainLayout>
  <router-view v-else />
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from './components/layout/MainLayout.vue'

const router = useRouter()
const isAuthenticated = ref(false)

const checkAuth = () => {
  // ✅ PERBAIKI: Cek token DAN user data untuk consistency
  const token = localStorage.getItem('auth_token') || 
                localStorage.getItem('token') || 
                localStorage.getItem('user_token')
  
  const user = localStorage.getItem('user')
  
  // ✅ PERBAIKI: Harus ada token DAN user data
  isAuthenticated.value = !!token && !!user
  
  console.log('Auth check:', { 
    isAuthenticated: isAuthenticated.value, 
    token: !!token,
    user: !!user
  })
}

// Handle storage events (when localStorage changes in other tabs)
const handleStorageChange = (event) => {
  if (event.key === 'auth_token' || event.key === 'token' || event.key === 'user') {
    checkAuth()
  }
}

// Handle custom event untuk sign out dari komponen lain
const handleSignOutEvent = () => {
  checkAuth()
}

onMounted(() => {
  checkAuth()
  
  // Listen untuk storage changes (other tabs)
  window.addEventListener('storage', handleStorageChange)
  
  // Listen untuk custom event dari komponen lain
  window.addEventListener('signOut', handleSignOutEvent)
})

onUnmounted(() => {
  window.removeEventListener('storage', handleStorageChange)
  window.removeEventListener('signOut', handleSignOutEvent)
})

// Watch route changes
watch(() => router.currentRoute.value.path, (newPath) => {
  console.log('Route changed to:', newPath)
  // ✅ PERBAIKI: Delay sedikit untuk pastikan auth state updated
  setTimeout(() => {
    checkAuth()
  }, 100)
})

// ✅ TAMBAHKAN: Watch untuk localStorage changes secara real-time
watch(() => localStorage.getItem('auth_token'), (newToken, oldToken) => {
  if (newToken !== oldToken) {
    checkAuth()
  }
})

watch(() => localStorage.getItem('user'), (newUser, oldUser) => {
  if (newUser !== oldUser) {
    checkAuth()
  }
})
</script>