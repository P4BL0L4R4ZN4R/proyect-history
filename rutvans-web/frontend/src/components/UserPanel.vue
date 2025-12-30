<template>
  <div class="user-panel-overlay" v-if="isOpen" @click="closePanel">
    <div class="user-panel" @click.stop>
      <div class="panel-header">
        <div class="user-info">
          <div class="user-avatar">
            <img v-if="user.profile_photo_path" :src="user.profile_photo_path" alt="User Photo" />
            <span v-else class="user-initial">
              {{ user.name ? user.name.charAt(0).toUpperCase() : 'U' }}
            </span>
          </div>
          <span class="user-name">{{ user.name || 'Usuario' }}</span>
        </div>
      </div>

      <div class="panel-content">
        <div class="panel-section">
          <h3 class="section-title">Configuración de cuenta</h3>
          <ul class="section-list">
            <li class="section-item">Datos personales</li>
            <li class="section-item">Mis favoritos</li>
            <li class="section-item">Historial de viajes</li>
            <li class="section-item">Cambio de contraseña</li>
            <li class="section-item">Administrar solicitudes</li>
          </ul>
        </div>

        <div class="panel-section">
          <h3 class="section-title">Ayuda y legal</h3>
          <ul class="section-list">
            <li class="section-item">Centro de ayuda</li>
            <li class="section-item">Términos y condiciones</li>
            <li class="section-item">Aviso de privacidad</li>
          </ul>
        </div>

        <div class="logout-section" @click="$emit('logout')">
          <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserPanel',
  props: {
    isOpen: {
      type: Boolean,
      default: false
    },
    user: {
      type: Object,
      default: () => ({})
    }
  },
  watch: {
    isOpen(newVal) {
      if (newVal) {
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';
      } else {
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';
      }
    }
  },
  methods: {
    closePanel() {
      this.$emit('close');
    }
  },
  beforeDestroy() {
    // Asegurarnos de restaurar el scroll cuando el componente se destruya
    document.body.style.overflow = '';
    document.body.style.position = '';
    document.body.style.width = '';
  }
};
</script>

<style scoped>
.user-panel-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 2000;
  display: flex;
  justify-content: flex-end;
}

.user-panel {
  width: 350px;
  height: 100%;
  background-color: white;
  box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
  }
  to {
    transform: translateX(0);
  }
}

.panel-header {
  background-color: var(--primary-color);
  padding: 20px;
  color: white;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 15px;
}

.user-avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(255, 255, 255, 0.2);
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.user-initial {
  font-size: 24px;
  font-weight: bold;
  color: white;
}

.user-name {
  font-size: 18px;
  font-weight: 600;
}

.panel-content {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
}

.panel-section {
  margin-bottom: 25px;
}

.section-title {
  color: var(--primary-color);
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 15px;
  padding-bottom: 8px;
  border-bottom: 1px solid #eee;
}

.section-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.section-item {
  padding: 12px 10px;
  cursor: pointer;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.section-item:hover {
  background-color: #f5f5f5;
}

.logout-section {
  padding: 15px 10px;
  color: #ff4444;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 600;
  margin-top: auto;
  border-top: 1px solid #eee;
}

.logout-section:hover {
  background-color: #ffeeee;
}

@media (max-width: 576px) {
  .user-panel {
    width: 85%;
  }
  
  .user-avatar {
    width: 50px;
    height: 50px;
  }
  
  .user-name {
    font-size: 16px;
  }
}
</style>