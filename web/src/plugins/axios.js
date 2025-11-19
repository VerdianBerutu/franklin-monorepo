import axios from 'axios'

// Base URL
axios.defaults.baseURL = 'http://127.0.0.1:8000/api'  // ganti localhost → 127.0.0.1 (lebih stabil di beberapa OS)
axios.defaults.withCredentials = true
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

// REQUEST INTERCEPTOR — Attach token
axios.interceptors.request.use(
  (config) => {
    // Coba semua kemungkinan nama token (paling aman)
    const token = localStorage.getItem('token') || 
                  localStorage.getItem('auth_token') || 
                  localStorage.getItem('sanctum_token') || 
                  null

    if (token) {
      config.headers.Authorization = `Bearer ${token}`
      console.log('Token attached:', token.substring(0, 20) + '...')
    } else {
      console.log('No token found')
    }

    console.log('Request:', config.method?.toUpperCase(), config.url)
    return config
  },
  (error) => Promise.reject(error)
)

// RESPONSE INTERCEPTOR — Handle error dengan benar
axios.interceptors.response.use(
  (response) => response,  // langsung return response, tidak perlu log berlebihan di production
  (error) => {
    const status = error.response?.status

    if (status === 401) {
      console.log('Unauthorized → Logout otomatis')
      // Hapus semua kemungkinan token
      localStorage.removeItem('token')
      localStorage.removeItem('auth_token')
      localStorage.removeItem('sanctum_token')
      localStorage.removeItem('user')

      // Redirect ke login (hanya jika belum di halaman login)
      if (!window.location.pathname.includes('/login')) {
        window.location.href = '/login'
      }
    }

    // Optional: handle 403, 404, 500 bisa ditambahkan notifikasi toast
    return Promise.reject(error)
  }
)

export default axios