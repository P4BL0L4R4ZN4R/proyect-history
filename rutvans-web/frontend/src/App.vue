<template>
  <div id="app">
    <MainHeader
      v-if="isAuthenticated && !$route.meta.hideHeader" 
      :currentUser="currentUser"
      :scrolled="scrolled"
      :menuOpen="menuOpen"
      :showUserMenu="showUserMenu"
      @logout="logout"
      @toggle-menu="toggleMenu"
      @toggle-user-menu="toggleUserMenu"
    />
    
    <transition name="fade" mode="out-in">
      <!-- Aplicamos clase dinámica para controlar padding -->
      <div
        class="contenido"
        :class="[
          { 'no-header': !isAuthenticated || $route.meta.hideHeader }, 
          { 'no-padding': isFullWidthRoute }
        ]"
      >
        <router-view />
      </div>
    
    </transition>

    <MainFooter v-if="isAuthenticated" />
  </div>
</template>

<script>
import MainHeader from './components/MainHeader.vue';
import MainFooter from './components/MainFooter.vue';

export default {
  name: 'App',
  components: {
    MainHeader,
    MainFooter,
  },
  data() {
    return {
      scrolled: false,
      isAuthenticated: false,
      currentUser: {
        name: '',
        profile_photo_path: '',
      },
      menuOpen: false,
      showUserMenu: false,
    };
  },
  computed: {
    isFullWidthRoute() {
      // Detecta si estamos en la ruta principal ("/") para evitar padding
      return this.$route.path === '/';
    }
  },
  created() {
    this.checkAuth();
  },
  methods: {
    checkAuth() {
      const token = localStorage.getItem('token');
      const user = localStorage.getItem('user');

      if (token && user) {
        this.isAuthenticated = true;
        this.currentUser = JSON.parse(user);
      } else {
        this.isAuthenticated = false;
        this.currentUser = {};
      }
    },
    logout() {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      this.isAuthenticated = false;
      this.currentUser = {};
      this.$router.push('/login');
    },
    handleScroll() {
      this.scrolled = window.scrollY > 10;
    },
    toggleMenu() {
      this.menuOpen = !this.menuOpen;
    },
    toggleUserMenu() {
      this.showUserMenu = !this.showUserMenu;
    },
  },
  watch: {
    $route() {
      this.checkAuth();
      this.showUserMenu = false;
    },
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll);
  },
  beforeDestroy() {
    window.removeEventListener('scroll', this.handleScroll);
  },
};
</script>

<style>
:root {
  --primary-color: #e67e22;
  --secondary-color: #000000;
  --accent-color: #010101;
  --light-color: #f8f9fa;
  --dark-color: #343a40;
  --text-light: #ffffff;
  --text-dark: #333333;
  --hover-color: #d35400;
}

html, body {
  margin: 0;
  padding: 0;
  overflow-x: hidden; /* Evita el scroll horizontal */
  width: 100%;
}

#app {
  font-family: 'Arial', Helvetica, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  margin: 0;
  color: var(--text-dark);
  background-color: transparent;
}

.contenido {
  flex: 1;
  width: 100%;
  background-color: white;
  margin-top: 70px; /* Solo si hay header */
  min-height: calc(100vh - 70px);
  box-sizing: border-box;
  padding: 30px;
}

.contenido.no-header {
  margin-top: 0;
  min-height: 100vh;
}

.contenido.no-padding {
  padding: 0 !important; /* Anula el padding solo para Home */
  box-shadow: none;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}

@media (max-width: 768px) {
  .contenido {
    margin-top: 120px;
    min-height: calc(100vh - 120px);
  }

  .contenido.no-header {
    margin-top: 0;
    min-height: 100vh;
  }
}
</style>
