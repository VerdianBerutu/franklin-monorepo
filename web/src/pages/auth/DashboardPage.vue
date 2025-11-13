<template>
  <div class="dashboard-page p-6 bg-gray-50 min-h-screen">
    <!-- Header -->
   

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Total Sales -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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

      <!-- Total Users -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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

      <!-- Total Products -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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

      <!-- Total Customers -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left Column -->
      <div class="lg:col-span-2 space-y-8">
        <!-- Monthly Sales Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Aksi Cepat</h2>
          <div class="space-y-3">
            <button class="w-full flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-colors group">
              <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200">
                  <i class="fas fa-chart-bar text-blue-600"></i>
                </div>
                <div class="text-left">
                  <p class="font-semibold text-gray-900">Lihat Laporan Penjualan</p>
                  <p class="text-sm text-gray-600">Abaci laporan penjualan henglap</p>
                </div>
              </div>
              <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600"></i>
            </button>
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

export default {
  name: 'DashboardPage',
  setup() {
    const months = ref(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'])
    
    const getRandomHeight = () => {
      return `${Math.floor(Math.random() * 120) + 40}px`
    }

    return {
      months,
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