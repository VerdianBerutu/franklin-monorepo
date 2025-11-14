import { usePermissions } from '@/composables/usePermissions'

export default {
  mounted(el, binding) {
    const { hasPermission, hasAnyPermission, hasAllPermissions, hasRole } = usePermissions()
    
    let hasAccess = false
    
    if (binding.arg === 'role') {
      hasAccess = hasRole(binding.value)
    } else if (binding.arg === 'any') {
      hasAccess = hasAnyPermission(binding.value)
    } else if (binding.arg === 'all') {
      hasAccess = hasAllPermissions(binding.value)
    } else {
      hasAccess = hasPermission(binding.value)
    }
    
    if (!hasAccess) {
      el.style.display = 'none'
    }
  }
}