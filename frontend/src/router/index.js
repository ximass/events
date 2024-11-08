import { createRouter, createWebHistory } from 'vue-router';
import UserLogin from '../components/UserLogin.vue';
import Dashboard from '../components/Dashboard.vue';
import UserRegister from '../components/UserRegister.vue';
import Checkin from '../components/Checkin.vue';

const routes = [
  { path: '/login', name: 'UserLogin', component: UserLogin },
  { path: '/register', name: 'UserRegister', component: UserRegister },
  { path: '/dashboard', name: 'Dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/checkin', name: 'Checkin', component: Checkin, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('authToken');

  if (to.name === 'UserLogin' && isAuthenticated) {
    next('/dashboard');
  } else if (to.matched.some(record => record.meta.requiresAuth) && !isAuthenticated) {
    next('/login');
  } else {
    next();
  }
});

export default router;
