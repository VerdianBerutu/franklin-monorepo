<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="flex justify-between items-center p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">
          {{ isEdit ? 'Edit User' : 'Add New User' }}
        </h2>
        <button 
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600"
        >
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
        <!-- Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Name <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.name }"
          />
          <p v-if="errors.name" class="mt-1 text-sm text-red-500">
            {{ errors.name[0] }}
          </p>
        </div>

        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Email <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.email }"
          />
          <p v-if="errors.email" class="mt-1 text-sm text-red-500">
            {{ errors.email[0] }}
          </p>
        </div>

        <!-- Role -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Role <span class="text-red-500">*</span>
          </label>
          <select
            v-model="form.role"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.role }"
          >
            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="staff">Staff</option>
            <option value="user">User</option>
          </select>
          <p v-if="errors.role" class="mt-1 text-sm text-red-500">
            {{ errors.role[0] }}
          </p>
        </div>

        <!-- Password (Required for new user, optional for edit) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Password 
            <span v-if="!isEdit" class="text-red-500">*</span>
            <span v-else class="text-gray-500 text-xs">(leave blank to keep current)</span>
          </label>
          <div class="relative">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              :required="!isEdit"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10"
              :class="{ 'border-red-500': errors.password }"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"
            >
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
          <p v-if="errors.password" class="mt-1 text-sm text-red-500">
            {{ errors.password[0] }}
          </p>
        </div>

        <!-- Password Confirmation -->
        <div v-if="form.password">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Confirm Password <span class="text-red-500">*</span>
          </label>
          <div class="relative">
            <input
              v-model="form.password_confirmation"
              :type="showPasswordConfirm ? 'text' : 'password'"
              :required="!!form.password"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10"
              :class="{ 'border-red-500': errors.password_confirmation }"
            />
            <button
              type="button"
              @click="showPasswordConfirm = !showPasswordConfirm"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"
            >
              <i :class="showPasswordConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
          <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-500">
            {{ errors.password_confirmation[0] }}
          </p>
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="p-4 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-sm text-red-600">{{ errorMessage }}</p>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="p-4 bg-green-50 border border-green-200 rounded-lg">
          <p class="text-sm text-green-600">{{ successMessage }}</p>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 pt-4">
          <button
            type="button"
            @click="$emit('close')"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
            :disabled="loading"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
            :disabled="loading"
          >
            <span v-if="!loading">{{ isEdit ? 'Update' : 'Create' }}</span>
            <span v-else class="flex items-center justify-center">
              <i class="fas fa-spinner fa-spin mr-2"></i>
              {{ isEdit ? 'Updating...' : 'Creating...' }}
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { userAPI } from '@/services/user'

const props = defineProps({
  user: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'saved'])

// State
const loading = ref(false)
const errors = ref({})
const errorMessage = ref('')
const successMessage = ref('')
const showPassword = ref(false)
const showPasswordConfirm = ref(false)

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: ''
})

// Computed
const isEdit = computed(() => !!props.user)

// Watch for user prop changes
watch(() => props.user, (newUser) => {
  if (newUser) {
    form.value.name = newUser.name
    form.value.email = newUser.email
    form.value.role = newUser.roles?.[0] || ''
    form.value.password = ''
    form.value.password_confirmation = ''
  }
}, { immediate: true })

// Methods
const handleSubmit = async () => {
  errors.value = {}
  errorMessage.value = ''
  successMessage.value = ''
  loading.value = true

  try {
    const payload = {
      name: form.value.name,
      email: form.value.email,
      role: form.value.role
    }

    // Only include password if it's provided
    if (form.value.password) {
      payload.password = form.value.password
      payload.password_confirmation = form.value.password_confirmation
    }

    if (isEdit.value) {
      await userAPI.updateUser(props.user.id, payload)
      successMessage.value = 'User updated successfully!'
    } else {
      await userAPI.createUser(payload)
      successMessage.value = 'User created successfully!'
    }

    setTimeout(() => {
      emit('saved')
    }, 1000)

  } catch (error) {
    console.error('Error saving user:', error)

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      errorMessage.value = error.response.data.message || 'Validation failed'
    } else {
      errorMessage.value = error.response?.data?.message || 'Failed to save user'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Prevent body scroll when modal is open */
:deep(body) {
  overflow: hidden;
}
</style>