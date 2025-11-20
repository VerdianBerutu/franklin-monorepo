import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  //  ROOT ROUTE - Redirect berdasarkan auth status
  {
    path: '/',
    redirect: (to) => {
      const token = localStorage.getItem('auth_token')
      const user = localStorage.getItem('user')
      const isLoggedIn = !!(token && user)
      
      return isLoggedIn ? '/dashboard' : '/login'
    }
  },
  // Auth Routes
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/pages/auth/LoginPage.vue'),
    meta: { requiresGuest: true }
  },
  {
  path: '/profile',
  name: 'profile',
  component: () => import('@/pages/management/ProfilePage.vue'),
  meta: { requiresAuth: true } // Proteksi + konsistensi
},
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/pages/auth/RegisterPage.vue'),
    meta: { requiresGuest: true }
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
  //  TAMBAHKAN: 404 Catch-all route
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    redirect: (to) => {
      const token = localStorage.getItem('auth_token')
      const user = localStorage.getItem('user')
      const isLoggedIn = !!(token && user)
      
      return isLoggedIn ? '/dashboard' : '/login'
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

//  NAVIGATION GUARD YANG DIPERBAIKI
router.beforeEach((to, from, next) => {
  // 🔍 Logging untuk debugging
  console.log('🔒 Router Guard:', {
    to: to.path,
    from: from.path,
    requiresAuth: to.meta.requiresAuth,
    requiresGuest: to.meta.requiresGuest
  })

  // 1️ Ambil data auth dari localStorage
  const token = localStorage.getItem('auth_token')
  const userStr = localStorage.getItem('user')
  
  // 2️ Parse user data dengan error handling
  let user = null
  try {
    user = userStr ? JSON.parse(userStr) : null
  } catch (e) {
    console.error('❌ Error parsing user data:', e)
    // Clear corrupted data
    localStorage.removeItem('user')
    localStorage.removeItem('auth_token')
  }
  
  // 3️ Check auth status
  const isLoggedIn = !!(token && user)
  
  console.log('Auth Status:', {
    hasToken: !!token,
    hasUser: !!user,
    isLoggedIn
  })

  // 4️ HANDLE ROUTES YANG BUTUH AUTH
  if (to.meta.requiresAuth) {
    if (!isLoggedIn) {
      console.log('❌ Not authenticated, redirecting to login')
      next({
        path: '/login',
        query: { redirect: to.fullPath } //  Save intended destination
      })
      return
    }
    
    //  OPTIONAL: Check permission
    if (to.meta.permission && user.permissions) {
      const hasPermission = user.permissions.includes(to.meta.permission)
      
      if (!hasPermission) {
        console.log('❌ No permission for:', to.meta.permission)
        // Redirect ke dashboard jika tidak ada permission
        next('/dashboard')
        return
      }
    }
    
    console.log(' Authenticated, allowing access')
    next()
    return
  }
  
  // 5️ HANDLE GUEST ROUTES (login, register)
  if (to.meta.requiresGuest) {
    if (isLoggedIn) {
      console.log(' Already authenticated, redirecting to dashboard')
      next('/dashboard')
      return
    }
    console.log('✅ Guest route, allowing access')
    next()
    return
  }
  
  // 6️⃣ PUBLIC ROUTES
  console.log(' Public route, allowing access')
  next()
})

//  OPTIONAL: Handle navigation errors
router.onError((error) => {
  console.error('❌ Router Navigation Error:', error)
})

export default router