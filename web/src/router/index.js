import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  // ✅ ROOT ROUTE
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/pages/auth/DashboardPage.vue'), 
    meta: { requiresAuth: true, permission: 'view dashboard' }
  },
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
    meta: { requiresAuth: true, permission: 'view products' } // ✅ DIPERBAIKI
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
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const hasToken = !!localStorage.getItem('auth_token')
  const hasUser = !!localStorage.getItem('user')
  const isLoggedIn = hasToken && hasUser
  
  if (to.meta.requiresAuth && !isLoggedIn) {
    next('/login')
    return
  }
  
  if (to.meta.requiresGuest && isLoggedIn) {
    next('/dashboard')
    return
  }
  
  next()
})

export default router