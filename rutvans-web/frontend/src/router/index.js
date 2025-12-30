import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import UsuarioView from "../views/UsuarioView.vue";
import PuntoVenta from '../views/PuntoVenta.vue';
import VentaAsientos from '@/views/VentaAsientos.vue';
import FormTicket from '@/views/FormTicket.vue';
import LoginView from '../views/LoginView.vue';
import RegisterView from '../views/RegisterView.vue';
import { useAuthStore } from '@/stores/auth';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { 
        requiresAuth: false, 
        hideHeader: true 
      }
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
      meta: { 
        requiresAuth: false, 
        hideHeader: true 
      }
    },
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { 
        requiresAuth: true,
        roles: ['client', 'cashier', 'admin', 'super-admin'] // Roles que pueden acceder
      }
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue'),
      meta: { requiresAuth: false }
    },
    {
      path: '/notificaciones',
      name: 'Notificaciones',
      component: () => import('../views/Notificaciones.vue'),
      meta: {
        hideHeader: true,
        requiresAuth: true,
        roles: ['client', 'cashier', 'admin', 'super-admin']
      }
    },
    {
      path: "/usuarios",
      name: "usuarios",
      component: UsuarioView,
      meta: { 
        requiresAuth: true,
        roles: ['client', 'admin', 'super-admin'] // Solo admin y super-admin
      }
    },
    {
      path: '/punto-venta',
      name: 'PuntoVenta',
      component: PuntoVenta,
      meta: { 
        requiresAuth: true,
        roles: ['client', 'cashier', 'admin', 'super-admin'] // Solo cajeros y admins
      }
    },
    {
      path: '/venta-asientos',
      name: 'VentaAsientos',
      component: VentaAsientos,
      meta: { 
        requiresAuth: true,
        roles: ['client', 'cashier', 'admin', 'super-admin'] // Solo cajeros y admins
      }
    },
    {
      path: '/form-ticket',
      name: 'FormTicket',
      component: FormTicket,
      props: true,
      meta: { 
        requiresAuth: true,
        roles: ['client', 'cashier', 'admin', 'super-admin']
      }
    },
    // NUEVA RUTA: Corte de Ventas - Solo para cajeros
    {
      path: '/corte-ventas',
      name: 'SalesCut',
      component: () => import('@/views/SalesCut.vue'),
      meta: { 
        requiresAuth: true,
        roles: ['cashier', 'admin', 'super-admin'] // Solo cajeros y admins
      }
    },
        {
      path: '/envios',
      name: 'EnvioView',
      component: () => import('@/views/EnvioView.vue'),
      meta: { 
        requiresAuth: true,
        roles: ['client', 'cashier', 'admin', 'super-admin'] // Solo cajeros y admins
      }
    },
  ]
})

// Guardia de navegación global mejorado
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  const isAuthenticated = localStorage.getItem('token')
  
  // Si no está autenticado y la ruta requiere autenticación
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
    return
  }
  
  // Si está autenticado pero intenta acceder al login/register
  if ((to.name === 'login' || to.name === 'register') && isAuthenticated) {
    next('/')
    return
  }

  // Si está autenticado, cargar los roles del usuario
  if (isAuthenticated && authStore.roles.length === 0) {
    try {
      await authStore.fetchUserRoles()
    } catch (error) {
      console.error('Error loading user roles:', error)
      // Si hay error al cargar roles, hacer logout
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      authStore.clearAuth()
      next('/login')
      return
    }
  }

  // Verificar roles si la ruta los requiere
  if (to.meta.roles && isAuthenticated && authStore.roles.length > 0) {
    const hasRequiredRole = to.meta.roles.some(role => authStore.hasRole(role))
    
    if (!hasRequiredRole) {
      // Usuario no tiene el rol requerido - redirigir a home
      console.warn(`Acceso denegado: El usuario no tiene los roles requeridos: ${to.meta.roles.join(', ')}`)
      next('/')
      return
    }
  }

  next()
})

export default router