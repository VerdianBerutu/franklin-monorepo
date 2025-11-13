// src/services/api.js
import axios from 'axios'

// 1) baseURL dari env (fallback ke localhost)
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

// 2) instance axios dengan timeout
export const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 20000,
  headers: {
    Accept: 'application/json',
    // NOTE: jangan set Content-Type global; biarkan axios menentukan per request
  },
})

// 3) Request interceptor – selipkan token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

// 4) Response interceptor – handle 401 dan normalize error
api.interceptors.response.use(
  (res) => res,
  (err) => {
    const status = err?.response?.status
    if (status === 401) {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      // Jika mau pakai Vue Router:
      // import router di sini (atau lempar event) lalu: router.replace({ name: 'login' })
      window.location.href = '/login'
    }
    return Promise.reject(err)
  }
)

/* =======================
   Domain APIs
======================= */

// Auth API
export const authAPI = {
  login: (email, password) => api.post('/login', { email, password }).then(r => r.data),
  register: (name, email, password, password_confirmation) =>
    api.post('/register', { name, email, password, password_confirmation }).then(r => r.data),
  logout: () => api.post('/logout').then(r => r.data),
  getCurrentUser: () => api.get('/me').then(r => r.data),
}

// Users API
export const usersAPI = {
  getAll: () => api.get('/users').then(r => r.data),
  getById: (id) => api.get(`/users/${id}`).then(r => r.data),
  create: (payload) => api.post('/users', payload).then(r => r.data),
  update: (id, payload) => api.put(`/users/${id}`, payload).then(r => r.data),
  delete: (id) => api.delete(`/users/${id}`).then(r => r.data),
}

// Products API
export const productsAPI = {
  getAll: () => api.get('/products').then(r => r.data),
}

// Customers API
export const customersAPI = {
  getAll: () => api.get('/customers').then(r => r.data),
}

// Dashboard API
export const dashboardAPI = {
  getStats: () => api.get('/dashboard/stats').then(r => r.data),
}

/* =======================
   Utilities (Export/Download)
======================= */

// Contoh helper export (CSV/XLS/PDF)
export const downloadAPI = {
  // server harus kirim Content-Disposition: attachment; filename="..."
  getReport: async (path, params = {}, filename = 'report.xlsx') => {
    const res = await api.get(path, { params, responseType: 'blob' })
    const url = URL.createObjectURL(new Blob([res.data]))
    const a = document.createElement('a')
    a.href = url
    a.download = filename
    document.body.appendChild(a)
    a.click()
    a.remove()
    URL.revokeObjectURL(url)
  }
}

export default api
