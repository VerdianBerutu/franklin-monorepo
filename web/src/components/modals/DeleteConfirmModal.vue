<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
      <!-- Icon -->
      <div class="p-6 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
        </div>
        
        <!-- Title -->
        <h3 class="text-xl font-bold text-gray-900 mb-2">
          Delete User
        </h3>
        
        <!-- Message -->
        <p class="text-gray-600 mb-4">
          Are you sure you want to delete <strong>{{ user.name }}</strong>?
        </p>
        <p class="text-sm text-gray-500 mb-6">
          This action cannot be undone. All data associated with this user will be permanently deleted.
        </p>

        <!-- User Info Card -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-3">
              <span class="text-red-600 font-semibold">
                {{ getInitials(user.name) }}
              </span>
            </div>
            <div class="flex-1">
              <div class="font-medium text-gray-900">{{ user.name }}</div>
              <div class="text-sm text-gray-500">{{ user.email }}</div>
              <div class="text-xs text-gray-400 mt-1">
                Role: {{ user.roles?.join(', ') || 'N/A' }}
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3">
          <button
            @click="$emit('close')"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
          >
            Cancel
          </button>
          <button
            @click="$emit('confirmed')"
            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
          >
            <i class="fas fa-trash mr-2"></i>
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

defineEmits(['close', 'confirmed'])

const getInitials = (name) => {
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}
</script>