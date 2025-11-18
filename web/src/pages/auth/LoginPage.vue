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
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Sign In</h1>
          <p class="text-gray-600">Enter your email and password to sign in!</p>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleSubmit" class="space-y-5">
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
              placeholder="info@gmail.com"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
              :class="{ 'border-red-500': errors.email }"
              :disabled="loading"
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
                :disabled="loading"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                :disabled="loading"
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

          <!-- Remember Me & Forgot Password -->
          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <input
                v-model="form.remember"
                type="checkbox"
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                :disabled="loading"
              />
              <span class="ml-2 text-sm text-gray-600">Keep me logged in</span>
            </label>
            <a href="#" class="text-sm text-blue-600 hover:text-blue-700">
              Forgot password?
            </a>
          </div>

          <!-- Error Message -->
          <div
            v-if="errorMessage"
            class="p-4 bg-red-50 border border-red-200 rounded-lg flex items-start"
          >
            <svg 
              class="w-5 h-5 text-red-600 mt-0.5 mr-2 flex-shrink-0" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path 
                fill-rule="evenodd" 
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" 
                clip-rule="evenodd" 
              />
            </svg>
            <p class="text-sm text-red-600">{{ errorMessage }}</p>
          </div>

          <!-- Success Message -->
          <div
            v-if="successMessage"
            class="p-4 bg-green-50 border border-green-200 rounded-lg flex items-start"
          >
            <svg 
              class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path 
                fill-rule="evenodd" 
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" 
                clip-rule="evenodd" 
              />
            </svg>
            <p class="text-sm text-green-600">{{ successMessage }}</p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
          >
            <span v-if="!loading">Sign In</span>
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
              Signing in...
            </span>
          </button>
        </form>

        <!-- Sign Up Link -->
        <p class="mt-6 text-center text-sm text-gray-600">
          Don't have an account?
          <router-link to="/register" class="text-blue-600 hover:text-blue-700 font-medium">
            Sign Up
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
  email: '',
  password: '',
  remember: false
})

const showPassword = ref(false)
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const errors = ref({})

const handleSubmit = async () => {
  // Reset messages
  errors.value = {}
  errorMessage.value = ''
  successMessage.value = ''

  // Client-side validation
  if (!form.value.email) {
    errors.value.email = 'Email is required'
    return
  }
  
  if (!form.value.password) {
    errors.value.password = 'Password is required'
    return
  }

  // Email format validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(form.value.email)) {
    errors.value.email = 'Please enter a valid email address'
    return
  }

  // Password length validation
  if (form.value.password.length < 6) {
    errors.value.password = 'Password must be at least 6 characters'
    return
  }

  loading.value = true

  try {
    console.log('🔐 Attempting login...')
    console.log('📧 Email:', form.value.email)
    
    // ✅ PERBAIKAN: Call API login (baseURL sudah include /api)
    const response = await axios.post('/login', {
      email: form.value.email,
      password: form.value.password
    })
    
    console.log('📦 Login Response:', response.data)
    
    if (response.data.success) {
      const { token, user } = response.data.data
      
      // ✅ PERBAIKAN: Transform data dari backend ke format yang diharapkan frontend
      const userData = {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.roles[0] || 'user', // Ambil role pertama dari array
        roles: user.roles, // Simpan semua roles (array)
        permissions: user.permissions || [] // Array permissions dari Spatie
      }
      
      console.log('💾 Saving to localStorage:', {
        token: token.substring(0, 20) + '...',
        user: userData
      })
      
      // ✅ Simpan ke localStorage dengan key yang konsisten
      localStorage.setItem('auth_token', token)
      localStorage.setItem('user', JSON.stringify(userData))
      
      // ✅ Verify data tersimpan
      console.log('✅ Verification:')
      console.log('  - Token saved:', !!localStorage.getItem('auth_token'))
      console.log('  - User saved:', !!localStorage.getItem('user'))
      
      // ✅ Show success message
      successMessage.value = 'Login successful! Redirecting...'
      
      // ✅ PENTING: Redirect dengan router.push
      setTimeout(() => {
        router.push('/dashboard')
      }, 500)
    }
    
  } catch (error) {
    console.error('❌ Login error:', error)
    
    // Handle berbagai tipe error
    if (error.response) {
      // Error dari server (4xx, 5xx)
      const status = error.response.status
      const data = error.response.data
      
      console.error('Server Error:', {
        status,
        data
      })
      
      switch (status) {
        case 401:
          errorMessage.value = 'Invalid email or password. Please try again.'
          break
        case 422:
          errorMessage.value = data.message || 'Validation error. Please check your input.'
          // Handle validation errors
          if (data.errors) {
            errors.value = data.errors
          }
          break
        case 429:
          errorMessage.value = 'Too many login attempts. Please try again later.'
          break
        case 500:
          errorMessage.value = 'Server error. Please try again later.'
          break
        default:
          errorMessage.value = data.message || 'Login failed. Please try again.'
      }
    } else if (error.request) {
      // Request dibuat tapi tidak ada response
      errorMessage.value = 'Cannot connect to server. Please check your internet connection.'
      console.error('No response from server:', error.request)
    } else {
      // Error lainnya
      errorMessage.value = error.message || 'An unexpected error occurred. Please try again.'
      console.error('Error:', error.message)
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Custom animations */
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
  20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.shake {
  animation: shake 0.5s;
}

/* Smooth transitions */
input:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

button:disabled {
  cursor: not-allowed;
}

/* Focus styles */
input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Error state animation */
.border-red-500 {
  animation: shake 0.3s;
}
</style>