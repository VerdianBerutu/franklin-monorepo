<template>
  <div class="sidebar bg-white border-r border-gray-200 fixed left-0 top-0 z-50 h-screen transition-all duration-300" 
       :class="isCollapsed ? 'w-20' : 'w-64'">
    
    <!-- Logo -->
 <div class="sidebar-header px-6 py-4 flex items-center justify-between">
      <div v-if="!isCollapsed" class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
          <span class="text-white font-bold text-sm">F</span>
        </div>
        <h1 class="text-xl font-bold text-gray-900">Franklin</h1>
      </div>
      <div v-else class="flex justify-center w-full">
        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
          <span class="text-white font-bold text-sm">F</span>
        </div>
      </div>
      
      <button @click="toggleSidebar" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
        <i class="fas fa-chevron-left text-gray-500 text-sm" 
           :class="{ 'transform rotate-180': isCollapsed }"></i>
      </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav flex-1 px-4 py-6 overflow-y-auto">
      <!-- MENU UTAMA -->
      <div class="mb-8">
        <h4 v-if="!isCollapsed" class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 px-2">
          MENU
        </h4>
        <ul class="space-y-1">
          <li>
            <router-link to="/dashboard" 
                        class="nav-item flex items-center px-3 py-3 rounded-lg transition-colors"
                        :class="isActive('/dashboard') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50'">
              <i class="fas fa-tachometer-alt text-lg w-6 text-center"></i>
              <span v-if="!isCollapsed" class="ml-3 font-medium">Dashboard</span>
            </router-link>
          </li>
          <li>
            <router-link to="/upload" 
                        class="nav-item flex items-center px-3 py-3 rounded-lg transition-colors text-gray-700 hover:bg-gray-50">
              <i class="fas fa-upload text-lg w-6 text-center"></i>
              <span v-if="!isCollapsed" class="ml-3 font-medium">Upload Files</span>
            </router-link>
          </li>
          <li>
            <a href="#" class="nav-item flex items-center px-3 py-3 rounded-lg transition-colors text-gray-700 hover:bg-gray-50">
              <i class="fas fa-chart-bar text-lg w-6 text-center"></i>
              <span v-if="!isCollapsed" class="ml-3 font-medium">Presentation/Regulation</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- LAINNYA -->
      <div>
        <h4 v-if="!isCollapsed" class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 px-2">
          LAINNYA
        </h4>
        <ul class="space-y-1">
          <li>
            <a href="#" class="nav-item flex items-center px-3 py-3 rounded-lg transition-colors text-gray-700 hover:bg-gray-50">
              <i class="fas fa-file-alt text-lg w-6 text-center"></i>
              <span v-if="!isCollapsed" class="ml-3 font-medium">Laporan</span>
            </a>
          </li>
          <li>
            <a href="#" class="nav-item flex items-center px-3 py-3 rounded-lg transition-colors text-gray-700 hover:bg-gray-50">
              <i class="fas fa-cog text-lg w-6 text-center"></i>
              <span v-if="!isCollapsed" class="ml-3 font-medium">Pengaturan</span>
            </a>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

export default {
  name: 'Sidebar',
  setup() {
    const route = useRoute()
    const isCollapsed = ref(false)

    const isActive = (path) => {
      return route.path === path
    }

    const toggleSidebar = () => {
      isCollapsed.value = !isCollapsed.value
      window.dispatchEvent(new CustomEvent('sidebarToggle', { 
        detail: { collapsed: isCollapsed.value } 
      }))
    }

    return {
      isCollapsed,
      isActive,
      toggleSidebar
    }
  }
}
</script>