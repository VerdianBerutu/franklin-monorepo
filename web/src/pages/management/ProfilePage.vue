<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden">
      <div class="px-8 py-6 bg-indigo-600">
        <h2 class="text-2xl font-bold text-white text-center">Ganti Password</h2>
      </div>

      <form @submit.prevent="updatePassword" class="px-8 py-6 space-y-6">
        <!-- Current Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
          <input
            v-model="form.current_password"
            type="password"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2 border"
          />
        </div>

        <!-- New Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Password Baru</label>
          <input
            v-model="form.password"
            type="password"
            required
            minlength="8"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2 border"
          />
        </div>

        <!-- Confirm Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2 border"
          />
        </div>

        <!-- Alert Success -->
        <div v-if="success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
          {{ success }}
        </div>

        <!-- Alert Error -->
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          {{ error }}
        </div>

        <div class="flex items-center justify-between">
          <button
            type="button"
            @click="$router.back()"
            class="text-sm text-gray-600 hover:text-gray-900"
          >
            ← Kembali
          </button>

          <button
            type="submit"
            :disabled="loading"
            class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 disabled:opacity-50"
          >
            {{ loading ? 'Menyimpan...' : 'Simpan Password' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '@/plugins/axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const form = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const loading = ref(false)
const success = ref('')
const error = ref('')

const updatePassword = async () => {
  loading.value = true
  success.value = ''
  error.value = ''

  try {
    await axios.post('/update-password', form.value)

    success.value = 'Password berhasil diubah! Logout otomatis dalam 3 detik...'
    
    // Kosongkan form
    form.value = {
      current_password: '',
      password: '',
      password_confirmation: ''
    }

    // Auto logout setelah 3 detik
    setTimeout(() => {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      router.push('/login')
      setTimeout(() => window.location.reload(), 100)
    }, 3000)

  } catch (err) {
    if (err.response?.data?.errors) {
      const errors = err.response.data.errors
      error.value = Object.values(errors).flat().join(' ')
    } else {
      error.value = err.response?.data?.message || 'Gagal mengubah password'
    }
  } finally {
    loading.value = false
  }
}
</script>