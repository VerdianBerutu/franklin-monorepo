// src/composables/useAuth.js
import { reactive, readonly } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI } from '@/services/auth'   // lihat bagian 2 di bawah

const state = reactive({
  user: JSON.parse(localStorage.getItem('user') || 'null'),
  loading: false,
  error: null,
})

export function useAuth() {
  const router = useRouter()

  const setUser = (u) => {
    state.user = u
    if (u) localStorage.setItem('user', JSON.stringify(u))
    else localStorage.removeItem('user')
  }

  const login = async (email, password) => {
    state.error = null
    state.loading = true
    try {
      const res = await authAPI.login(email, password)
      // backend diharapkan mengembalikan { success, data: { user, token } }
      const { user, token } = res.data
      localStorage.setItem('auth_token', token)
      setUser(user)
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
      localStorage.setItem('auth_token', token)
      setUser(user)
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
    try { await authAPI.logout() } catch {}
    localStorage.removeItem('auth_token')
    setUser(null)
    router.replace({ name: 'login' })
  }

  const hasRole = (role) => !!state.user?.roles?.includes(role)
  const hasPermission = (perm) => !!state.user?.permissions?.includes(perm)
  const isAdmin = () => hasRole('admin')
  const isAuthenticated = () => !!localStorage.getItem('auth_token')

  return {
    user: readonly(state).user,
    loading: readonly(state).loading,
    error: readonly(state).error,
    login,
    register,
    logout,
    getCurrentUser,
    hasRole,
    hasPermission,
    isAdmin,
    isAuthenticated,
  }
}
