import axios from 'axios'

// ✅ KONFIGURASI BASE URL - Sesuaikan dengan backend Laravel Anda
axios.defaults.baseURL = 'http://localhost:8000/api' // Sudah include /api
axios.defaults.withCredentials = true
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

// ✅ REQUEST INTERCEPTOR - Attach token ke setiap request
axios.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token')
    
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
      console.log('🔑 Token attached to request:', token.substring(0, 20) + '...')
    } else {
      console.log('⚠️ No token found for request')
    }
    
    console.log('📤 Request:', {
      method: config.method?.toUpperCase(),
      url: config.url,
      baseURL: config.baseURL
    })
    
    return config
  },
  (error) => {
    console.error('❌ Request Error:', error)
    return Promise.reject(error)
  }
)

// ✅ RESPONSE INTERCEPTOR - Handle response dan error
axios.interceptors.response.use(
  (response) => {
    console.log('📥 Response:', {
      status: response.status,
      url: response.config.url,
      data: response.data
    })
    return response
  },
  (error) => {
    console.error('❌ Response Error:', {
      status: error.response?.status,
      url: error.config?.url,
      message: error.response?.data?.message || error.message
    })
    
    // Handle 401 Unauthorized - Token expired atau invalid
    if (error.response?.status === 401) {
      console.log('🚫 Unauthorized - Clearing auth data and redirecting to login')
      
      // Clear auth data
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      
      // Redirect ke login (hanya jika tidak sedang di halaman login)
      if (!window.location.pathname.includes('/login')) {
        window.location.href = '/login'
      }
    }
    
    // Handle 403 Forbidden - No permission
    if (error.response?.status === 403) {
      console.log('🚫 Forbidden - No permission')
      // Optional: Redirect ke dashboard atau 403 page
    }
    
    // Handle 404 Not Found
    if (error.response?.status === 404) {
      console.log('🔍 Not Found - Resource tidak ditemukan')
    }
    
    // Handle 500 Internal Server Error
    if (error.response?.status === 500) {
      console.log('💥 Server Error - Ada masalah di backend')
    }
    
    return Promise.reject(error)
  }
)

export default axios