import { computed } from 'vue'
import { useAuth } from './useAuth'

export function usePermissions() {
  const { user, isAuthenticated } = useAuth()

  const hasPermission = (permission) => {
    // PERBAIKAN: user adalah readonly reactive, bukan ref
    if (!user || !user.permissions) {
      return false
    }
    return user.permissions.includes(permission)
  }

  const hasAnyPermission = (permissions) => {
    if (!user || !user.permissions) {
      return false
    }
    return permissions.some(permission => user.permissions.includes(permission))
  }

  const hasAllPermissions = (permissions) => {
    if (!user || !user.permissions) {
      return false
    }
    return permissions.every(permission => user.permissions.includes(permission))
  }

  const hasRole = (role) => {
    if (!user || !user.roles) {
      return false
    }
    return user.roles.includes(role)
  }

  return {
    hasPermission,
    hasAnyPermission,
    hasAllPermissions,
    hasRole
  }
}