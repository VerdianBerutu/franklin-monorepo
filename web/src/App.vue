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
  // Cek multiple possible token names untuk compatibility
  const token = localStorage.getItem('auth_token') || 
                localStorage.getItem('token') || 
                localStorage.getItem('user_token')
  
  isAuthenticated.value = !!token
  console.log('Auth check:', { isAuthenticated: isAuthenticated.value, token })
}

// Handle storage events (when localStorage changes in other tabs)
const handleStorageChange = (event) => {
  if (event.key === 'auth_token' || event.key === 'token') {
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
  
  // Initial check
  checkAuth()
})

onUnmounted(() => {
  window.removeEventListener('storage', handleStorageChange)
  window.removeEventListener('signOut', handleSignOutEvent)
})

// Watch route changes
watch(() => router.currentRoute.value.path, (newPath) => {
  console.log('Route changed to:', newPath)
  checkAuth()
})
</script>