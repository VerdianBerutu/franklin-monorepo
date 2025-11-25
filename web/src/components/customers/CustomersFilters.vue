<!-- web/src/components/customers/CustomersFilters.vue -->
<template>
  <div class="mb-6 flex flex-col sm:flex-row gap-4">
    <div class="flex-1">
      <input
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        @keyup.enter="$emit('filter')"
        type="text"
        placeholder="Cari nama, perusahaan, email, phone..."
        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
      />
    </div>
    <button
      @click="$emit('filter')"
      class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 whitespace-nowrap flex items-center gap-2"
    >
      <i class="fas fa-search"></i> Filter
    </button>
  </div>
</template>

<script setup>
defineProps({
  modelValue: String
})
defineEmits(['update:modelValue', 'filter'])
</script>