<template>
  <div class="min-h-screen flex">
    <!-- Left Side - Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
      <div class="w-full max-w-md">
        <!-- Back to Dashboard Link -->
        <router-link
          to="/"
          class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-8"
        >
          <svg
            class="w-4 h-4 mr-2"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 19l-7-7 7-7"
            />
          </svg>
          Back to dashboard
        </router-link>

        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Sign Up</h1>
          <p class="text-gray-600">Enter your email and password to sign up!</p>
        </div>

      
        
        <!-- Register Form -->
        <form @submit.prevent="handleSubmit" class="space-y-5">
          <!-- Name Inputs -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="first-name" class="block text-sm font-medium text-gray-700 mb-2">
                First Name<span class="text-red-500">*</span>
              </label>
              <input
                id="first-name"
                v-model="form.first_name"
                type="text"
                required
                placeholder="Enter your first name"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                :class="{ 'border-red-500': errors.first_name }"
              />
              <p v-if="errors.first_name" class="mt-1 text-sm text-red-500">
                {{ errors.first_name }}
              </p>
            </div>
            <div>
              <label for="last-name" class="block text-sm font-medium text-gray-700 mb-2">
                Last Name<span class="text-red-500">*</span>
              </label>
              <input
                id="last-name"
                v-model="form.last_name"
                type="text"
                required
                placeholder="Enter your last name"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                :class="{ 'border-red-500': errors.last_name }"
              />
              <p v-if="errors.last_name" class="mt-1 text-sm text-red-500">
                {{ errors.last_name }}
              </p>
            </div>
          </div>

          <!-- Email Input -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email<span class="text-red-500">*</span>
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              placeholder="Enter your email"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
              :class="{ 'border-red-500': errors.email }"
            />
            <p v-if="errors.email" class="mt-1 text-sm text-red-500">
              {{ errors.email }}
            </p>
          </div>

          <!-- Password Input -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Password<span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                placeholder="Enter your password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition pr-10"
                :class="{ 'border-red-500': errors.password }"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
              >
                <svg
                  v-if="!showPassword"
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  />
                </svg>
                <svg
                  v-else
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                  />
                </svg>
              </button>
            </div>
            <p v-if="errors.password" class="mt-1 text-sm text-red-500">
              {{ errors.password }}
            </p>
          </div>

          <!-- Terms and Conditions -->
          <div class="flex items-start">
            <input
              id="agree-terms"
              v-model="form.agree_terms"
              type="checkbox"
              class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-1"
            />
            <label for="agree-terms" class="ml-2 text-sm text-gray-600">
              By creating an account means you agree to the 
              <a href="#" class="text-blue-600 hover:text-blue-700">Terms and Conditions</a>, 
              and our 
              <a href="#" class="text-blue-600 hover:text-blue-700">Privacy Policy</a>
            </label>
          </div>

          <!-- Error Message -->
          <div
            v-if="errorMessage"
            class="p-4 bg-red-50 border border-red-200 rounded-lg"
          >
            <p class="text-sm text-red-600">{{ errorMessage }}</p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="!loading">Sign Up</span>
            <span v-else class="flex items-center justify-center">
              <svg
                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              Creating Account...
            </span>
          </button>
        </form>

        <!-- Sign In Link -->
        <p class="mt-6 text-center text-sm text-gray-600">
          Already have an account?
          <router-link to="/login" class="text-blue-600 hover:text-blue-700 font-medium">
            Sign In
          </router-link>
        </p>
      </div>
    </div>

    <!-- Right Side - Branding -->
    <div
      class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#1C2434] to-[#313D4F] items-center justify-center p-12"
    >
      <div class="text-center">
        <!-- Logo/Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-600 rounded-2xl mb-8">
          <svg
            class="w-12 h-12 text-white"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
            />
          </svg>
        </div>

        <!-- Title -->
        <h2 class="text-4xl font-bold text-white mb-4">Franklin Offshore Indonesia Perkasa</h2>
        <p class="text-xl text-gray-300 max-w-md mx-auto">
          YOUR GLOBAL PARTNER FOR INTEGRATED RIGGING AND MOORING SOLUTIONS
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  agree_terms: false
})

const showPassword = ref(false)
const loading = ref(false)
const errorMessage = ref('')
const errors = ref({})

// API Base URL - update this if backend runs on different port
const API_BASE_URL = 'http://localhost:8000/api'

const handleSubmit = async () => {
  errors.value = {}
  errorMessage.value = ''

  // Basic validation
  if (!form.value.first_name) {
    errors.value.first_name = 'First name is required'
    return
  }
  if (!form.value.last_name) {
    errors.value.last_name = 'Last name is required'
    return
  }
  if (!form.value.email) {
    errors.value.email = 'Email is required'
    return
  }
  if (!form.value.password) {
    errors.value.password = 'Password is required'
    return
  }
  if (!form.value.agree_terms) {
    errorMessage.value = 'Please agree to the terms and conditions'
    return
  }

  loading.value = true

  try {
    console.log('Attempting registration to:', `${API_BASE_URL}/register`)
    
    const response = await axios.post(`${API_BASE_URL}/register`, {
      name: `${form.value.first_name} ${form.value.last_name}`,
      email: form.value.email,
      password: form.value.password,
      password_confirmation: form.value.password
    })

    console.log('Registration response:', response.data)

    if (response.data.success || response.data.token) {
      const token = response.data.token || response.data.data?.token
      const user = response.data.user || response.data.data?.user
      
      if (!token) {
        throw new Error('No token received from server')
      }
      
      // Save to localStorage
      localStorage.setItem('auth_token', token)
      localStorage.setItem('user', JSON.stringify(user || {
        name: `${form.value.first_name} ${form.value.last_name}`,
        email: form.value.email
      }))
      
      // Set axios default header
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
      
      console.log('Registration successful, redirecting to dashboard...')
      
      // Redirect ke dashboard
      router.push('/dashboard')
      
    } else {
      errorMessage.value = response.data.message || 'Registration failed'
    }
    
  } catch (error) {
    console.error('Registration error:', error)
    
    if (error.response?.data?.errors) {
      // Handle validation errors from backend
      const backendErrors = error.response.data.errors
      if (backendErrors.email) {
        errorMessage.value = backendErrors.email[0]
      } else if (backendErrors.password) {
        errorMessage.value = backendErrors.password[0]
      } else {
        errorMessage.value = 'Please check your input and try again.'
      }
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else if (error.request) {
      errorMessage.value = 'Cannot connect to server. Please check if backend is running.'
    } else {
      errorMessage.value = 'Registration failed. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>