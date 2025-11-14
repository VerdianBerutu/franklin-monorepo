<template>
  <div class="dashboard-page p-6 bg-gray-50 min-h-screen">
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

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Total Sales - ✅ Sudah benar -->
      <div v-permission="'view sales'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Total Sales</h3>
          <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-dollar-sign text-blue-600"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900 mb-2">Rp 0.00</p>
        <div class="flex items-center text-green-600 text-sm">
          <i class="fas fa-arrow-up mr-1"></i>
          <span>+12.5% vs last month</span>
        </div>
      </div>

      <!-- Total Users - ✅ Sudah benar -->
      <div v-permission="'view users'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Total Users</h3>
          <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-users text-green-600"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900 mb-2">1,248</p>
        <div class="flex items-center text-green-600 text-sm">
          <i class="fas fa-arrow-up mr-1"></i>
          <span>+8.2% vs last month</span>
        </div>
      </div>

      <!-- Total Products - ✅ GANTI ke 'view uploads' -->
      <div v-permission="'view uploads'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Total Products</h3>
          <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-box text-purple-600"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900 mb-2">856</p>
        <div class="flex items-center text-green-600 text-sm">
          <i class="fas fa-arrow-up mr-1"></i>
          <span>+15.3% vs last month</span>
        </div>
      </div>

      <!-- Total Customers - ✅ Sudah benar -->
      <div v-permission="'view customers'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Total Customers</h3>
          <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-user-friends text-orange-600"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900 mb-2">3,782</p>
        <div class="flex items-center text-green-600 text-sm">
          <i class="fas fa-arrow-up mr-1"></i>
          <span>+5.7% vs last month</span>
        </div>
      </div>
    </div>

    <!-- Quick Actions Berdasarkan Permissions -->
    <!-- ✅ GANTI permission check untuk create products -->
    <div v-permission:any="['create users', 'create uploads', 'create customers', 'create sales', 'export reports']" 
         class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Aksi Cepat</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Manage Users - ✅ Sudah benar -->
        <div v-permission="'create users'" class="bg-blue-50 p-4 rounded-lg border border-blue-200">
          <h3 class="font-semibold text-blue-900">Kelola Pengguna</h3>
          <p class="text-sm text-blue-700 mt-1">Tambah atau edit pengguna sistem</p>
          <router-link 
            to="/users"
            class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm"
          >
            Ke Pengguna
          </router-link>
        </div>

        <!-- Manage Products - ✅ GANTI ke 'create uploads' & 'edit uploads' -->
        <div v-permission:any="['create uploads', 'edit uploads']" class="bg-green-50 p-4 rounded-lg border border-green-200">
          <h3 class="font-semibold text-green-900">Kelola Produk</h3>
          <p class="text-sm text-green-700 mt-1">Tambah atau edit produk</p>
          <router-link 
            to="/products"
            class="inline-block mt-3 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm"
          >
            Ke Produk
          </router-link>
        </div>

        <!-- View Reports - ✅ Sudah benar -->
        <div v-permission="'view reports'" class="bg-purple-50 p-4 rounded-lg border border-purple-200">
          <h3 class="font-semibold text-purple-900">Lihat Laporan</h3>
          <p class="text-sm text-purple-700 mt-1">Akses laporan sistem</p>
          <button class="mt-3 px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm">
            Lihat Laporan
          </button>
        </div>

        <!-- Export Data - ✅ Sudah benar -->
        <div v-permission="'export reports'" class="bg-orange-50 p-4 rounded-lg border border-orange-200">
          <h3 class="font-semibold text-orange-900">Ekspor Data</h3>
          <p class="text-sm text-orange-700 mt-1">Ekspor laporan dan data</p>
          <button class="mt-3 px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 text-sm">
            Ekspor Sekarang
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left Column -->
      <div class="lg:col-span-2 space-y-8">
        <!-- Monthly Sales Chart - ✅ Sudah benar -->
        <div v-permission="'view sales'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Monthly Sales</h2>
            <div class="flex items-center space-x-2">
              <span class="text-sm text-gray-600">Monthly Target</span>
              <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
            </div>
          </div>
          
          <!-- Chart Placeholder -->
          <div class="h-64 flex items-end justify-between px-4 mb-4">
            <div v-for="(month, index) in months" :key="month" class="flex flex-col items-center flex-1 mx-1">
              <div class="chart-bar bg-blue-500 rounded-t w-full max-w-8 transition-all duration-300" 
                   :style="{ height: getRandomHeight() }"></div>
              <span class="text-xs text-gray-600 mt-2">{{ month }}</span>
            </div>
          </div>
          
          <div class="flex justify-center">
            <div class="inline-flex rounded-lg border border-gray-200 p-1">
              <button class="px-3 py-1 text-sm font-medium rounded-md bg-blue-600 text-white">Monthly</button>
              <button class="px-3 py-1 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">Quarterly</button>
              <button class="px-3 py-1 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">Annually</button>
            </div>
          </div>
        </div>

        <!-- Quick Actions Alternative -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Navigasi Cepat</h2>
          <div class="space-y-3">
            <!-- Products Link - ✅ GANTI ke 'view uploads' -->
            <router-link 
              v-permission="'view uploads'"
              to="/products"
              class="w-full flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-colors group"
            >
              <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200">
                  <i class="fas fa-box text-blue-600"></i>
                </div>
                <div class="text-left">
                  <p class="font-semibold text-gray-900">Kelola Produk</p>
                  <p class="text-sm text-gray-600">Lihat dan kelola produk</p>
                </div>
              </div>
              <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600"></i>
            </router-link>

            <!-- Customers Link - ✅ Sudah benar -->
            <router-link 
              v-permission="'view customers'"
              to="/customers"
              class="w-full flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:border-green-500 hover:bg-green-50 transition-colors group"
            >
              <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200">
                  <i class="fas fa-user-friends text-green-600"></i>
                </div>
                <div class="text-left">
                  <p class="font-semibold text-gray-900">Kelola Pelanggan</p>
                  <p class="text-sm text-gray-600">Lihat dan kelola pelanggan</p>
                </div>
              </div>
              <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-600"></i>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div class="space-y-8">
        <!-- Monthly Target -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-2">Monthly Target</h2>
          <p class="text-gray-600 mb-6">Target you've set for each month.</p>
          
          <!-- Progress Section -->
          <div class="space-y-6">
            <div class="space-y-4">
              <div class="flex justify-between text-sm">
                <span class="font-medium text-gray-700">Target</span>
                <span class="font-semibold text-blue-600">75.55%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: 75.55%"></div>
              </div>
            </div>
            
            <div class="space-y-4">
              <div class="flex justify-between text-sm">
                <span class="font-medium text-gray-700">Revenue</span>
                <span class="font-semibold text-green-600">100%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
              </div>
            </div>
          </div>

          <!-- Achievement Message -->
          <div class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200">
            <div class="flex items-start">
              <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-1">
                <i class="fas fa-trophy text-green-600 text-sm"></i>
              </div>
              <div>
                <p class="text-green-800 font-medium">You earn $3287 today, it's higher than last month.</p>
                <p class="text-green-600 text-sm mt-1">Keep up your good work!</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistics -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Statistics</h2>
          <div class="space-y-4">
            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
              <span class="font-medium text-gray-700">Target</span>
              <div class="flex items-center text-green-600">
                <span class="font-semibold">$20K</span>
                <i class="fas fa-arrow-up ml-1 text-xs"></i>
              </div>
            </div>
            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
              <span class="font-medium text-gray-700">Revenue</span>
              <div class="flex items-center text-green-600">
                <span class="font-semibold">$20K</span>
                <i class="fas fa-arrow-up ml-1 text-xs"></i>
              </div>
            </div>
            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
              <span class="font-medium text-gray-700">Today</span>
              <div class="flex items-center text-green-600">
                <span class="font-semibold">$20K</span>
                <i class="fas fa-arrow-up ml-1 text-xs"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Aktivitas Terbaru</h2>
          <div class="space-y-4">
            <div class="flex items-start space-x-3">
              <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mt-1">
                <i class="fas fa-user text-blue-600 text-sm"></i>
              </div>
              <div class="flex-1">
                <p class="font-medium text-gray-900">User login berhasil</p>
                <p class="text-sm text-gray-600">2 menit yang lalu</p>
              </div>
            </div>
            <div class="flex items-start space-x-3">
              <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mt-1">
                <i class="fas fa-tachometer-alt text-green-600 text-sm"></i>
              </div>
              <div class="flex-1">
                <p class="font-medium text-gray-900">Dashboard dimuat</p>
                <p class="text-sm text-gray-600">2 menit yang lalu</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { usePermissions } from '@/composables/usePermissions'

export default {
  name: 'DashboardPage',
  setup() {
    const months = ref(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'])
    const { hasRole } = usePermissions()
    
    const getRandomHeight = () => {
      return `${Math.floor(Math.random() * 120) + 40}px`
    }

    return {
      months,
      hasRole,
      getRandomHeight
    }
  }
}
</script>

<style scoped>
.chart-bar {
  min-height: 20px;
}

/* Smooth transitions for sidebar */
.sidebar {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom scrollbar for sidebar */
.sidebar-nav::-webkit-scrollbar {
  width: 4px;
}

.sidebar-nav::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.sidebar-nav::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

.sidebar-nav::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>