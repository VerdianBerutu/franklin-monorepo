import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  // ROOT ROUTE - Redirect berdasarkan permission
  {
  path: '/',
  redirect: '/dashboard' // ✅ Simple redirect
},

  // Auth Routes
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/pages/auth/LoginPage.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/pages/auth/RegisterPage.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('@/pages/management/ProfilePage.vue'),
    meta: { requiresAuth: true }
  },

  // Dashboard
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/pages/auth/DashboardPage.vue'), 
    meta: { requiresAuth: true, permission: 'view dashboard' }
  },

  // Management Routes
  {
    path: '/users',
    name: 'Users',
    component: () => import('@/pages/management/UsersPage.vue'),
    meta: { requiresAuth: true, permission: 'view users' }
  },
  {
    path: '/products',
    name: 'Products',
    component: () => import('@/pages/management/ProductsPage.vue'),
    meta: { requiresAuth: true, permission: 'view products' }
  },
  {
    path: '/customers',
    name: 'Customers',
    component: () => import('@/pages/management/CustomersPage.vue'),
    meta: { requiresAuth: true, permission: 'view customers' }
  },
  {
    path: '/sales',
    name: 'Sales',
    component: () => import('@/pages/management/SalesPage.vue'),
    meta: { requiresAuth: true, permission: 'view sales' }
  },
  {
    path: '/certificates',
    name: 'Certificates',
    component: () => import('@/pages/management/CertificatesPage.vue'),
    meta: { requiresAuth: true, permission: 'view certificates' }
  },

  // 404 Catch-all route
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    redirect: '/dashboard'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Helper function untuk redirect ke halaman yang user bisa akses
function getDefaultRouteForUser(user) {
  if (!user || !user.permissions) {
     return '/dashboard'
  }

  // Priority order: dashboard > certificates > products > customers > sales
  if (user.permissions.includes('view dashboard')) {
    return '/dashboard'
  } else if (user.permissions.includes('view certificates')) {
    return '/certificates'
  } else if (user.permissions.includes('view products')) {
    return '/products'
  } else if (user.permissions.includes('view customers')) {
    return '/customers'
  } else if (user.permissions.includes('view sales')) {
    return '/sales'
  }

   return '/dashboard'
}

// NAVIGATION GUARD
router.beforeEach((to, from, next) => {
  console.log('🔒 Router Guard:', {
    to: to.path,
    from: from.path,
    requiresAuth: to.meta.requiresAuth,
    requiresGuest: to.meta.requiresGuest
  })

  // 1. Ambil data auth dari localStorage
  const token = localStorage.getItem('auth_token')
  const userStr = localStorage.getItem('user')
  
  // 2. Parse user data dengan error handling
  let user = null
  try {
    user = userStr ? JSON.parse(userStr) : null
  } catch (e) {
    console.error('❌ Error parsing user data:', e)
    localStorage.removeItem('user')
    localStorage.removeItem('auth_token')
  }
  
  // 3. Check auth status
  const isLoggedIn = !!(token && user)
  
  console.log('Auth Status:', {
    hasToken: !!token,
    hasUser: !!user,
    isLoggedIn,
    permissions: user?.permissions || []
  })

  // 4. HANDLE ROUTES YANG BUTUH AUTH
  if (to.meta.requiresAuth) {
    if (!isLoggedIn) {
      console.log('❌ Not authenticated, redirecting to login')
      next({
        path: '/login',
        query: { redirect: to.fullPath }
      })
      return
    }
    
    //  FIX: Check permission dengan redirect yang benar
    if (to.meta.permission && user.permissions) {
      const hasPermission = user.permissions.includes(to.meta.permission)
      
      if (!hasPermission) {
        console.log('❌ No permission for:', to.meta.permission)
        
        //  FIX: Redirect ke halaman yang user BISA akses
        const defaultRoute = getDefaultRouteForUser(user)
        
        // Prevent infinite loop
        if (to.path === defaultRoute) {
          console.error('⚠️ Circular redirect detected, logging out')
          localStorage.removeItem('user')
          localStorage.removeItem('auth_token')
          next('/login')
          return
        }
        
        next(defaultRoute)
        return
      }
    }
    
    console.log(' Authenticated, allowing access')
    next()
    return
  }
  
  // 5. HANDLE GUEST ROUTES (login, register)
  if (to.meta.requiresGuest) {
    if (isLoggedIn) {
      console.log(' Already authenticated, redirecting to default route')
      const defaultRoute = getDefaultRouteForUser(user)
      next(defaultRoute)
      return
    }
    console.log(' Guest route, allowing access')
    next()
    return  
  }
  
  // 6. PUBLIC ROUTES
  console.log('✅ Public route, allowing access')
  next()
})

// Handle navigation errors
router.onError((error) => {
  console.error('❌ Router Navigation Error:', error)
})

export default router