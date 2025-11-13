<template>
  <div class="main-layout flex">
    <Sidebar />
    <div class="main-content flex-1 transition-all duration-300" 
         :class="isSidebarCollapsed ? 'ml-20' : 'ml-64'">
      <Topbar />
      <main class="content">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'
import Sidebar from './Sidebar.vue'
import Topbar from './Topbar.vue'

export default {
  name: 'MainLayout',
  components: {
    Sidebar,
    Topbar
  },
  setup() {
    const isSidebarCollapsed = ref(false)

    const handleSidebarToggle = (event) => {
      isSidebarCollapsed.value = event.detail.collapsed
    }

    onMounted(() => {
      window.addEventListener('sidebarToggle', handleSidebarToggle)
    })

    onUnmounted(() => {
      window.removeEventListener('sidebarToggle', handleSidebarToggle)
    })

    return {
      isSidebarCollapsed
    }
  }
}
</script>

<style scoped>
.main-layout {
  min-height: 100vh;
  background-color: #f9fafb;
}
</style>