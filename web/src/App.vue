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
  const token = localStorage.getItem('auth_token')
  const user = localStorage.getItem('user')
  
  isAuthenticated.value = !!token && !!user
  
  console.log('Auth check:', { 
    isAuthenticated: isAuthenticated.value, 
    hasToken: !!token,
    hasUser: !!user,
    token: token ? token.substring(0, 20) + '...' : null
  })
  
  return isAuthenticated.value
}

const handleStorageChange = (event) => {
  if (event.key === 'auth_token' || event.key === 'user') {
    console.log('Storage changed:', event.key)
    checkAuth()
  }
}

const handleSignOutEvent = () => {
  console.log('Sign out event received')
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  checkAuth()
  router.push('/login')
}

onMounted(() => {
  checkAuth()
  window.addEventListener('storage', handleStorageChange)
  window.addEventListener('signOut', handleSignOutEvent)
})

onUnmounted(() => {
  window.removeEventListener('storage', handleStorageChange)
  window.removeEventListener('signOut', handleSignOutEvent)
})

// Watch route changes
watch(() => router.currentRoute.value.path, () => {
  checkAuth()
})
</script>