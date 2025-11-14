// src/composables/useAuth.js
import { reactive, readonly, computed } from 'vue'
import { authAPI } from '@/services/auth'

const state = reactive({
  user: JSON.parse(localStorage.getItem('user') || 'null'),
  loading: false,
  error: null,
})

export function useAuth() {
  // ❌ HAPUS: const router = useRouter() - ini yang bikin error!

  const setUser = (u) => {
    state.user = u
    if (u) {
      localStorage.setItem('user', JSON.stringify(u))
    } else {
      localStorage.removeItem('user')
    }
  }

  // ✅ checkAuth function yang robust
  const checkAuth = async () => {
    const token = localStorage.getItem('auth_token')
    
    // Jika tidak ada token, clear user
    if (!token) {
      setUser(null)
      return false
    }

    // Jika sudah ada user di state, return true
    if (state.user) {
      return true
    }

    // Fetch current user dari API
    try {
      await getCurrentUser()
      return true
    } catch (error) {
      console.error('Auth check failed:', error)
      // Clear invalid token
      localStorage.removeItem('auth_token')
      setUser(null)
      return false
    }
  }

  // ✅ Login dengan clear cache sebelumnya
  const login = async (email, password) => {
    state.error = null
    state.loading = true
    try {
      const res = await authAPI.login(email, password)
      const { user, token } = res.data
      
      // ✅ CLEAR DULU sebelum set baru (prevent cache issues)
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      
      // ✅ SET YANG BARU
      localStorage.setItem('auth_token', token)
      setUser(user)
      
      // ✅ LOG untuk debug
      console.log('Login successful! User permissions:', user.permissions)
      
      // ✅ REDIRECT KE DASHBOARD setelah login sukses
      window.location.href = '/dashboard'
      
    } catch (e) {
      state.error = e?.response?.data?.message || e?.message || 'Login failed'
      throw e
    } finally {
      state.loading = false
    }
  }

  const register = async (name, email, password, passwordConfirmation) => {
    state.error = null
    state.loading = true
    try {
      const res = await authAPI.register(name, email, password, passwordConfirmation)
      const { user, token } = res.data
      
      // ✅ CLEAR & SET sama seperti login
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      
      localStorage.setItem('auth_token', token)
      setUser(user)
      
      // ✅ REDIRECT KE DASHBOARD setelah register sukses
      window.location.href = '/dashboard'
      
    } catch (e) {
      state.error = e?.response?.data?.message || e?.message || 'Registration failed'
      throw e
    } finally {
      state.loading = false
    }
  }

  const getCurrentUser = async () => {
    try {
      const res = await authAPI.getCurrentUser()
      const me = res.data?.data || res.data
      setUser(me)
      return me
    } catch (e) {
      await logout()
      throw e
    }
  }

  const logout = async () => {
    try { 
      await authAPI.logout() 
    } catch (error) {
      console.warn('Logout API error:', error)
    }
    
    // ✅ PASTIKAN semua clear
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
    setUser(null)
    
    console.log('Logged out successfully')
    
    // ✅ Redirect ke login
    window.location.href = '/login'
  }

  const hasRole = (role) => {
    const roles = state.user?.roles || []
    return roles.includes(role)
  }
  
  const hasPermission = (perm) => {
    const permissions = state.user?.permissions || []
    return permissions.includes(perm)
  }
  
  const isAdmin = () => hasRole('admin')
  
  // ✅ isAuthenticated logic
  const isAuthenticated = computed(() => {
    const token = localStorage.getItem('auth_token')
    // Return true jika ada token
    return !!token
  })

  return {
    user: readonly(state).user,
    loading: readonly(state).loading,
    error: readonly(state).error,
    isAuthenticated,
    login,
    register,
    logout,
    getCurrentUser,
    checkAuth,
    hasRole,
    hasPermission,
    isAdmin,
  }
}