import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './style.css'
import permissionDirective from './directives/permission'
import axios from 'axios'

// ✅ Setup Axios
axios.defaults.baseURL = 'http://localhost:8000/api'

// Set token dari localStorage jika ada
const token = localStorage.getItem('auth_token')
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
  console.log('✅ Axios: Token set in headers')
} else {
  console.log('⚠️ Axios: No token found')
}

// Response interceptor untuk handle 401
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      console.error('❌ 401 Unauthorized - Auto logout')
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

// ✅ Create app instance
const app = createApp(App)

// ✅ Register plugins & directives
app.use(router)
app.directive('permission', permissionDirective)

// ✅ Mount app
app.mount('#app')

console.log('🚀 App mounted successfully')