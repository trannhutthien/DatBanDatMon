import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/HomeView.vue'),
    },
    {
      path: '/order',
      name: 'order',
      component: () => import('../views/OrderView.vue'),
    },
    {
      path: '/booked-tables',
      name: 'booked-tables',
      component: () => import('../views/BookedTablesView.vue'),
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../components/form/RegisterView.vue'),
    },
    {
      path: '/auth/callback',
      name: 'auth-callback',
      component: () => import('../components/auth/AuthCallback.vue'),
    },
    // Admin Routes
    {
      path: '/admin/users',
      name: 'admin-users',
      component: () => import('../views/admin/AccountUser.vue'),
    },
    {
      path: '/admin/restaurants',
      name: 'admin-restaurants',
      component: () => import('../views/admin/AccountNhaHang.vue'),
    },
  ],
})

export default router
