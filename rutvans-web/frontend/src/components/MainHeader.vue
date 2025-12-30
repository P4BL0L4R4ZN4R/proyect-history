<template>
  <header class="app-header" :class="{ 'scrolled-header': scrolled }">
    <div class="header-container">
      <div class="logo-container">
        <img src="../assets/LogoRutvans.png" alt="RUTVANS Logo" class="logo" />
        <span class="brand-name">
          <span class="rut">RUT</span><span class="vans">VANS</span>
        </span>
      </div>
      <nav class="main-nav" :class="{ 'nav-open': menuOpen }">
        <router-link to="/" @click.native="closeMenu">Inicio</router-link>
        <router-link to="/punto-venta" @click.native="closeMenu">Punto de Venta</router-link>
        <router-link to="/envios" @click.native="closeMenu">Envíos</router-link>
        <router-link to="/alquilar" @click.native="closeMenu">Alquilar Servicio</router-link>
        
        <!-- Solo visible con permiso view_sales_cut -->
        <router-link 
          v-if="authStore.can('view_sales_cut')"
          to="/corte-ventas" 
          @click.native="closeMenu"
          
        > Corte de Ventas
        </router-link>
      </nav>
      <div class="user-actions">
        <div class="user-menu">
          <button class="login-btn">
            <router-link to="/notificaciones" class="notification-link">
              <div class="notification-badge-container">
                <i class="fas fa-bell notification-icon"></i>
                <span v-if="unreadCount > 0" class="notification-badge">
                  {{ unreadCount }}
                </span>
              </div>
            </router-link>
            <div class="user-profile" @click="showUserPanel = true">
              <div class="user-avatar">
                <img v-if="currentUser.profile_photo_path" :src="currentUser.profile_photo_path" alt="User Photo" class="user-photo" />
                <span v-else class="user-initial" :style="{ backgroundColor: currentUser.name ? 'var(--primary-color)' : '#ccc' }">
                  {{ currentUser.name ? currentUser.name.charAt(0).toUpperCase() : 'U' }}
                </span>
              </div>
              <span class="user-name">{{ currentUser.name || 'Usuario' }}</span>
            </div>
          </button>
        </div>
      </div>
      <button class="menu-toggle" @click="$emit('toggle-menu')">
        <i class="fas fa-bars"></i>
      </button>
    </div>

    <!-- Panel lateral del usuario -->
    <UserPanel 
      :isOpen="showUserPanel" 
      :user="currentUser"
      @close="showUserPanel = false"
      @logout="$emit('logout')"
    />
  </header>
</template>

<script>
  import { ref, onMounted } from 'vue'
  import UserPanel from './UserPanel.vue';
  import { useAuthStore } from '@/stores/auth';
  import api from '@/api'

  export default {
    name: 'MainHeader',
    components: {
      UserPanel
    },
    props: {
      currentUser: {
        type: Object,
        default: () => ({}),
      },
      scrolled: {
        type: Boolean,
        default: false,
      },
      menuOpen: {
        type: Boolean,
        default: false,
      },
    },
    setup() {
      const authStore = useAuthStore();
      const unreadCount = ref(0);

      onMounted(async () => {
        try {
          const response = await api.get('/notifications-data-notread')
          const notifications = response.data.data || []
          unreadCount.value = notifications.filter(n => n.status === 'notread').length
        } catch (err) {
          unreadCount.value = 0
        }
      });

      return { 
        authStore,
        unreadCount 
      };
    },
    data() {
      return {
        showUserPanel: false
      };
    },
    methods: {
      closeMenu() {
        this.$emit('toggle-menu');
        this.showUserPanel = false;
      },
      handleBodyScroll(enable) {
        document.body.style.overflow = enable ? '' : 'hidden';
        document.body.style.position = enable ? '' : 'fixed';
        document.body.style.width = enable ? '' : '100%';
      }
    },
    watch: {
      showUserPanel(newVal) {
        this.handleBodyScroll(!newVal);
      }
    },
    beforeDestroy() {
      this.handleBodyScroll(true);
    }
  };
</script>

<style scoped>
.app-header,
.main-nav a,
button,
.social-icons a {
  transition: all 0.3s ease-in-out;
}

.app-header {
  background-color: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  position: fixed;
  width: 100%;
  top: 0;
  z-index: 1000;
}

.scrolled-header {
  background-color: white;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
  padding: 0.5rem 2rem;
}

.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1500px;
  margin: 0 auto;
  padding: 15px 20px;
}

.logo-container {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-right: auto;
}

.logo {
  height: 40px;
  transition: transform 0.3s ease;
}

.logo:hover {
  transform: scale(1.05);
}

.brand-name {
  font-size: 1.5rem;
  font-weight: bold;
  font-family: Arial, sans-serif;
  display: flex;
  align-items: center;
}

.rut {
  color: var(--secondary-color);
  font-weight: bold;
}

.vans {
  color: var(--primary-color);
  font-weight: bold;
}

.main-nav {
  display: flex;
  gap: 30px;
  margin-left: auto;
  margin-right: 2rem;
}

.main-nav a {
  color: var(--text-dark);
  text-decoration: none;
  font-weight: 500;
  font-size: 1.1rem;
  padding: 8px 0;
  position: relative;
}

.main-nav a:hover {
  color: var(--primary-color);
  background-color: transparent !important;
}

.main-nav a.router-link-exact-active {
  color: var(--primary-color);
  font-weight: 600;
}

/* Estilos específicos para el enlace de Corte de Ventas */
.corte-ventas-link {
  background: linear-gradient(45deg, #28a745, #20c997);
  color: white !important;
  border-radius: 8px;
  padding: 8px 16px !important;
  margin: 0 5px;
  font-weight: 600 !important;
  box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
}

.corte-ventas-link:hover {
  background: linear-gradient(45deg, #218838, #1e9e8a) !important;
  color: white !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
}

.corte-ventas-link.router-link-exact-active {
  background: linear-gradient(45deg, #1e7e34, #1a756e) !important;
  color: white !important;
}

.user-actions {
  display: flex;
  align-items: center;
}

.user-menu {
  position: relative;
  display: flex;
  align-items: center;
}

.login-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  background: transparent;
  border: none;
  cursor: pointer;
  font-size: 1rem;
}

.user-profile {
  display: flex;
  align-items: center;
  cursor: pointer;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 10px;
}

.user-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.user-initial {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
  font-size: 1.2rem;
}

.user-name {
  color: var(--text-dark);
  font-weight: 600;
  margin-right: 8px;
  transition: color 0.3s ease, transform 0.3s ease;
}

.user-name:hover {
  color: var(--primary-color);
  transform: translateY(-2px);
}

.notification-icon {
  color: #666;
  font-size: 1.2rem;
  transition: color 0.3s ease, transform 0.3s ease;
}

.notification-icon:hover {
  color: var(--primary-color);
  transform: scale(1.1);
}

.menu-toggle {
  display: none;
  background: none;
  color: var(--text-dark);
  font-size: 1.5rem;
  padding: 10px;
  border: none;
  cursor: pointer;
}

.notification-link {
  color: inherit;
  text-decoration: none;
  display: flex;
  align-items: center;
}

.notification-icon {
  color: #666;
  font-size: 1.2rem;
  transition: color 0.3s ease, transform 0.3s ease;
  cursor: pointer;
}

.notification-icon:hover {
  color: var(--primary-color);
  transform: scale(1.1);
}

.notification-badge-container {
  position: relative;
  display: inline-block;
}

.notification-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #e74c3c;
  color: white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  font-size: 0.7rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Asegúrate que el router-link no afecte el layout */
.login-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  background: transparent;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  padding: 0;
}

@media (max-width: 768px) {
  .header-container {
    flex-direction: column;
    padding: 10px;
  }
  .logo-container {
    margin-right: 0;
  }
  .main-nav {
    display: none;
    flex-direction: column;
    width: 100%;
    background-color: white;
    padding: 1rem;
    position: absolute;
    top: 100%;
    left: 0;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    flex-wrap: wrap;
    gap: 15px;
  }
  .main-nav.nav-open {
    display: flex;
  }
  .main-nav a {
    padding: 12px;
    font-size: 1.2rem;
    text-align: center;
  }
  
  /* Estilos responsivos para Corte de Ventas */
  .corte-ventas-link {
    padding: 12px 20px !important;
    margin: 5px 0;
    text-align: center;
  }
  
  .user-actions {
    margin-left: auto;
    width: 100%;
    justify-content: center;
    margin-top: 15px;
  }
  .user-menu {
    width: 100%;
  }
  .login-btn {
    width: 100%;
    justify-content: center;
  }
  .menu-toggle {
    display: block;
  }
}

@media (max-width: 576px) {
  .app-header {
    padding: 0.75rem;
  }
  .logo {
    height: 40px;
  }
  .brand-name {
    font-size: 1.4rem;
  }
  .login-btn {
    padding: 8px 15px;
    font-size: 0.9rem;
  }
  .user-avatar {
    width: 35px;
    height: 35px;
    margin-right: 8px;
  }
  .user-name {
    font-size: 0.9rem;
  }
  .notification-icon {
    font-size: 1rem;
  }
  
  .main-nav {
    gap: 10px;
  }
  
  .main-nav a {
    font-size: 1.1rem;
    padding: 10px;
  }
}
</style>