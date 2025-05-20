import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import DashboardView from '@/views/DashboardView.vue'
import AppointmentsView from '@/views/AppointmentsView.vue'
import CreateAppointmentView from '@/views/CreateAppointmentView.vue'
import CalendarView from '@/views/CalendarView.vue'
import ApiDocsView from '@/views/ApiDocsView.vue'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'dashboard',
      component: DashboardView,
      meta: { requiresAuth: true }
    },
    {
      path: '/dashboard',
      redirect: '/'
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView
    },
    {
      path: '/appointments',
      name: 'appointments',
      component: AppointmentsView,
      meta: { requiresAuth: true }
    },
    {
      path: '/appointments/create',
      name: 'create-appointment',
      component: CreateAppointmentView,
      meta: { requiresAuth: true }
    },
    {
      path: '/calendar',
      name: 'calendar',
      component: CalendarView,
      meta: { requiresAuth: true }
    },
    {
      path: '/docs',
      name: 'api-docs',
      component: ApiDocsView
    }
  ]
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else {
    next()
  }
})

export default router 