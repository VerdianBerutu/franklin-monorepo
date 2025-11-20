<template>
  <div class="dashboard-page p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
      <p class="text-gray-600 mt-2">
        <span v-if="hasRole('admin')">
          Selamat datang, Administrator! Anda memiliki akses penuh ke sistem.
        </span>
        <span v-else-if="hasRole('staff')">
          Selamat datang, Staff! Anda dapat mengelola data tetapi tidak dapat menghapus atau mengelola pengguna.
        </span>
        <span v-else>
          Selamat datang! Anda memiliki akses baca-saja ke data sistem.
        </span>
      </p>
    </div>

    <!-- Stats Grid - 100% DINAMIS & REAL -->
   <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
      <!-- TOTAL CERTIFICATES -->
      <div v-permission="'view certificates'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Total Certificates</h3>
          <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-certificate text-indigo-600"></i>
          </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">
          {{ loading ? '...' : stats.total_certificates }}
        </p>
        <div class="flex items-center text-green-600 text-sm mt-2">
          <i class="fas fa-sync mr-1"></i>
          <span>Real-time update</span>
        </div>
      </div>

      <!-- TOTAL USERS -->
      <div v-permission="'view users'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Total Users</h3>
          <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-users text-green-600"></i>
          </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">
          {{ loading ? '...' : stats.total_users }}
        </p>
        <div class="flex items-center text-green-600 text-sm mt-2">
          <i class="fas fa-arrow-up mr-1"></i>
          <span>Real time update</span>
        </div>
      </div>

      <!-- TOTAL PRODUCTS (dari tabel uploads) -->
      <div v-permission="'view uploads'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Total Products</h3>
          <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-box text-purple-600"></i>
          </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">
          {{ loading ? '...' : stats.total_products }}
        </p>
        <div class="flex items-center text-green-600 text-sm mt-2">
          <i class="fas fa-arrow-up mr-1"></i>
          <span>+15.3% vs last month</span>
        </div>
      </div>

      <!-- TOTAL CUSTOMERS (sementara 0 atau nanti diganti) -->
      <div v-permission="'view customers'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Total Customers</h3>
          <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-user-friends text-orange-600"></i>
          </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">
          {{ loading ? '...' : stats.total_customers }}
        </p>
        <div class="flex items-center text-green-600 text-sm mt-2">
          <i class="fas fa-arrow-up mr-1"></i>
          <span>+5.7% vs last month</span>
        </div>
      </div>
    </div>

      <div class="mt-12">
      <TrendChart />
    </div>
    <!-- Bagian lain (Quick Actions, Chart, dll) bisa kamu tambahkan nanti -->
    <!-- Untuk sekarang fokus ke stats dulu -->
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { usePermissions } from '@/composables/usePermissions'
import axios from '@/plugins/axios'   // ini yang paling umum

import TrendChart from '@/components/TrendChart.vue'
const { hasRole } = usePermissions()

// State stats
const stats = ref({
  total_users: 0,
  total_certificates: 0,
  total_products: 0,
  total_customers: 0,
})
const loading = ref(true)

// Fetch data dari API
const fetchStats = async () => {
  loading.value = true
  try {
    const response = await axios.get('/dashboard/stats')
    stats.value = response.data.data
  } catch (error) {
    console.error('Gagal mengambil data dashboard:', error)
    // Jangan biarkan error bikin crash
  } finally {
    loading.value = false
  }
}

// Auto load + real-time update
onMounted(() => {
  fetchStats()
  window.addEventListener('dashboard:refresh', fetchStats)
})

onUnmounted(() => {
  window.removeEventListener('dashboard:refresh', fetchStats)
})
</script>

<style scoped>
/* Optional: biar loading lebih smooth */
.text-3xl {
  font-size: 2.125rem;
  line-height: 2.5rem;
}
</style>