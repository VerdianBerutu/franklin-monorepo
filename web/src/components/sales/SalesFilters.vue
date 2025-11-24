<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
    <div class="flex flex-col md:flex-row gap-4">
      <!-- Search -->
      <div class="flex-1">
        <div class="relative">
          <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          <input
            :value="search"
            @input="$emit('update:search', $event.target.value)"
            type="text"
            placeholder="Search by invoice, customer, product..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
      </div>

      <!-- Date Range -->
      <div class="w-full md:w-64">
        <input
          :value="dateRange"
          @input="$emit('update:dateRange', $event.target.value)"
          type="date"
          placeholder="Date Range"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>

      <!-- Status Filter -->
      <div class="w-full md:w-48">
        <select
          :value="status"
          @change="$emit('update:status', $event.target.value)"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
          <option value="">All Status</option>
          <option value="paid">Paid</option>
          <option value="pending">Pending</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>

      <!-- Filter Button -->
      <button
        @click="$emit('filter')"
        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition whitespace-nowrap"
      >
        <i class="fas fa-filter mr-2"></i>
        Filter
      </button>

      <!-- Export Button -->
      <button
        @click="$emit('export')"
        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition whitespace-nowrap"
      >
        <i class="fas fa-download mr-2"></i>
        Export
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  search: String,
  dateRange: String,
  status: String
})

defineEmits(['update:search', 'update:dateRange', 'update:status', 'filter', 'export'])
</script>