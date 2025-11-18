// src/composables/useAuth.js
import { reactive, readonly, computed } from 'vue'
import { authAPI } from '@/services/auth'

const state = reactive({
  user: JSON.parse(localStorage.getItem('user') || 'null'),
  loading: false,
  error: null,
})

export function useAuth() {
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

  // ✅ Login dengan struktur response yang benar
  const login = async (email, password) => {
    state.error = null
    state.loading = true
    try {
      const res = await authAPI.login(email, password)
      
      // ✅ PERBAIKAN: Handle struktur response dari backend
      // Backend mengirim: { success: true, data: { user: {...}, token: "..." } }
      const responseData = res.data.data || res.data
      const { user, token } = responseData
      
      // Validasi data yang diterima
      if (!token) {
        throw new Error('No token received from server')
      }
      
      if (!user) {
        throw new Error('No user data received from server')
      }
      
      // ✅ CLEAR localStorage terlebih dahulu (prevent cache issues)
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      
      // ✅ SET data baru
      localStorage.setItem('auth_token', token)
      setUser(user)
      
      // ✅ LOG untuk debugging
      console.log('✅ Login successful!')
      console.log('User:', user)
      console.log('Roles:', user.roles)
      console.log('Permissions:', user.permissions)
      
      return { user, token }
      
    } catch (e) {
      const errorMessage = e?.response?.data?.message || e?.message || 'Login failed'
      state.error = errorMessage
      console.error('❌ Login error:', errorMessage)
      throw e
    } finally {
      state.loading = false
    }
  }

  // ✅ Register dengan struktur response yang benar
  const register = async (name, email, password, passwordConfirmation) => {
    state.error = null
    state.loading = true
    try {
      const res = await authAPI.register(name, email, password, passwordConfirmation)
      
      // ✅ PERBAIKAN: Handle struktur response dari backend
      const responseData = res.data.data || res.data
      const { user, token } = responseData
      
      // Validasi data yang diterima
      if (!token) {
        throw new Error('No token received from server')
      }
      
      if (!user) {
        throw new Error('No user data received from server')
      }
      
      // ✅ CLEAR localStorage terlebih dahulu
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      
      // ✅ SET data baru
      localStorage.setItem('auth_token', token)
      setUser(user)
      
      // ✅ LOG untuk debugging
      console.log('✅ Registration successful!')
      console.log('User:', user)
      console.log('Roles:', user.roles)
      console.log('Permissions:', user.permissions)
      
      return { user, token }
      
    } catch (e) {
      const errorMessage = e?.response?.data?.message || e?.message || 'Registration failed'
      state.error = errorMessage
      console.error('❌ Registration error:', errorMessage)
      throw e
    } finally {
      state.loading = false
    }
  }

  // ✅ Get current user dari API
  const getCurrentUser = async () => {
    try {
      const res = await authAPI.getCurrentUser()
      
      // Handle berbagai struktur response
      const userData = res.data?.data?.user || res.data?.data || res.data?.user || res.data
      
      if (!userData) {
        throw new Error('No user data received')
      }
      
      setUser(userData)
      console.log('✅ Current user fetched:', userData)
      
      return userData
    } catch (e) {
      console.error('❌ Get current user error:', e)
      await logout()
      throw e
    }
  }

  // ✅ Logout function
  const logout = async () => {
    try { 
      await authAPI.logout() 
      console.log('✅ Logout API call successful')
    } catch (error) {
      console.warn('⚠️ Logout API error:', error)
    }
    
    // ✅ PASTIKAN semua data ter-clear
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
    setUser(null)
    
    console.log('✅ Logged out successfully, localStorage cleared')
    
    // ✅ Redirect ke login dengan force reload
    window.location.href = '/login'
  }

  // ✅ Helper functions untuk check role & permission
  const hasRole = (role) => {
    if (!state.user || !state.user.roles) {
      return false
    }
    return state.user.roles.includes(role)
  }
  
  const hasPermission = (perm) => {
    if (!state.user || !state.user.permissions) {
      return false
    }
    return state.user.permissions.includes(perm)
  }
  
  const hasAnyRole = (roles) => {
    if (!state.user || !state.user.roles) {
      return false
    }
    return roles.some(role => state.user.roles.includes(role))
  }
  
  const hasAnyPermission = (permissions) => {
    if (!state.user || !state.user.permissions) {
      return false
    }
    return permissions.some(perm => state.user.permissions.includes(perm))
  }
  
  const isAdmin = () => hasRole('admin')
  
  // ✅ Computed isAuthenticated
  const isAuthenticated = computed(() => {
    const token = localStorage.getItem('auth_token')
    return !!token && !!state.user
  })

  return {
    // State (readonly untuk prevent mutation dari luar)
    user: readonly(state).user,
    loading: readonly(state).loading,
    error: readonly(state).error,
    isAuthenticated,
    
    // Methods
    login,
    register,
    logout,
    getCurrentUser,
    checkAuth,
    hasRole,
    hasPermission,
    hasAnyRole,
    hasAnyPermission,
    isAdmin,
  }
}