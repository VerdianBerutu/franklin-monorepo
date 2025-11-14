<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Customers Management</h1>
        <p class="text-gray-600 mt-1">Kelola data pelanggan</p>
      </div>
      <button 
        v-permission="'create customers'"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center"
      >
        <i class="fas fa-plus mr-2"></i>
        Add Customer
      </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <p class="text-gray-600">Customers management content here...</p>
    </div>
  </div>
</template>

<script setup>
// Component logic
</script>