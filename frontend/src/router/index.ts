import { createMemoryHistory, createRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import DashboardView from '@/views/travelOrders/DashboardView.vue';
import LoginPage from '@/views/auth/LoginPage.vue';
import RegisterView from '@/views/auth/RegisterView.vue';


const router = createRouter({
  history: createMemoryHistory(),
  routes: [
    {
      path: '/',
      redirect: () => {
        const authStore = useAuthStore();
        return authStore.isAuthenticated ? '/dashboard' : '/auth/login';
      },
    },
    {
      path: '/auth/login',
      name: 'Login',
      component: LoginPage,
      meta: { requiresAuth: false },
    },
    {
      path: '/auth/register',
      name: 'Register',
      component: RegisterView,
      meta: { requiresAuth: false },
    },
    {
      path: '/dashboard',
      name: 'TravelOrders',
      component: DashboardView,
      meta: { requiresAuth: false },
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: { template: '<div>404 - Página não encontrada</div>' },
    },
  ],
});

export default router;
