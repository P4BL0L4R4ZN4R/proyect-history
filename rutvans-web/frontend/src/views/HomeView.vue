<template>
  <div class="home-container">
    <!-- Carousel Banner -->
    <div class="carousel-banner">
      <div class="carousel-slide" v-for="(slide, index) in slides" :key="index" :class="{ 'active': index === currentSlide }">
        <img :src="slide.image" :alt="slide.title" class="carousel-image">
        <div class="carousel-text">
          <h2>{{ slide.title }}</h2>
          <p>{{ slide.description }}</p>
        </div>
      </div>
    </div>

    <!-- Filtro de Viajes - Solo visible para rol client/passenger -->
    <div class="filtro-viajes" v-if="showTravelFilter">
      <h2>Busca tu viaje</h2>
      <div class="filtro-container">
        <div class="filtro-item">
          <label for="origen">Origen:</label>
          <select id="origen" v-model="origen" :disabled="loadingOrigenes">
            <option value="">Selecciona un origen</option>
            <option v-for="origen in origenes" :key="origen" :value="origen">{{ origen }}</option>
          </select>
        </div>
        <div class="filtro-item">
          <label for="destino">Destino:</label>
          <select id="destino" v-model="destino" :disabled="loadingOrigenes || !origen">
            <option value="">Selecciona un destino</option>
            <option v-for="destino in destinosFiltrados" :key="destino" :value="destino">{{ destino }}</option>
          </select>
        </div>
        <button @click="aplicarFiltro" :disabled="!origen || !destino || loadingOrigenes" class="btn-filtrar">
          Buscar viajes
        </button>
      </div>
      <p v-if="loadingOrigenes">Cargando opciones...</p>
      <p v-if="errorOrigenes" class="error">{{ errorOrigenes }}</p>
    </div>

    <!-- Recent Trips Section - Título dinámico según rol -->
    <div class="recent-trips-section">
      <h2>{{ sectionTitle }}</h2>
      <RecentTrips />
    </div>

    <!-- Rutvans Móvil Banner -->
    <div class="app-banner">
      <h2>Rutvans Móvil</h2>
      <p>Próximamente</p>
    </div>

    <!-- Cards Section - Carrusel de Servicios -->
    <div class="services-section">
      <div class="services-container">
        <h2 class="section-title">Nuestros Servicios</h2>
        <p class="section-subtitle">Soluciones integrales para tu negocio de transporte</p>

        <div class="services-carousel-wrapper">
          <div class="services-carousel" :style="{ transform: `translateX(-${currentService * 100}%)` }">
            <div class="service-card" v-for="(servicio, index) in servicios" :key="index" :class="{ 'coming-soon': servicio.soon }">
              <div class="card-icon">
                <component :is="servicio.icon" size="28" />
              </div>
              <h3>{{ servicio.titulo }}</h3>
              <p>{{ servicio.descripcion }}</p>
              <div class="card-footer">
                <span>{{ servicio.soon ? 'Próximamente' : 'Explorar servicio' }}</span>
                <i v-if="!servicio.soon" class="fas fa-arrow-right"></i>
              </div>
              <div v-if="servicio.soon" class="coming-soon-badge">Nuevo</div>
              <div class="card-highlight"></div>
            </div>
          </div>
        </div>
        <div class="carousel-dots">
          <span v-for="(servicio, index) in servicios" 
                :key="index" 
                :class="{ active: index === currentService }"
                @click="goToService(index)"></span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Truck, BusFront, Store } from 'lucide-vue-next';
import RecentTrips from '@/components/RecentTrips.vue';
import api from '@/api';
import { useAuthStore } from '@/stores/auth';

export default {
  name: 'HomeView',
  components: {
    Truck,
    BusFront,
    Store,
    RecentTrips,
  },
  setup() {
    const authStore = useAuthStore();
    return { authStore };
  },
  data() {
    return {
      slides: [
        { image: '/slide1.png', title: 'Rutvans: Ofreciendo servicios e innovación', description: 'Digitalizando el transporte interurbano' },
        { image: '/slide2.jpg', title: '10 Lugares para visitar en Puebla 2024', description: 'Vive y disfruta de los mejores lugares turísticos!' },
        { image: '/slide3.jpg', title: 'Estas vaciones, conoce Cancún', description: '¡Planea tu viaje y conoce sus playas!' },
        { image: '/slide4.jpg', title: 'Las 13 mejores Playas de Veracruz', description: 'Conoce el encanto de ellas!' }
      ],
      currentSlide: 0,
      slideInterval: null,
      servicios: [
        {
          icon: 'Store',
          titulo: 'Punto de Venta',
          descripcion: 'Sistema completo para gestionar y optimizar tus ventas de boletos',
          soon: false,
        },
        {
          icon: 'Truck',
          titulo: 'Envíos',
          descripcion: 'Plataforma logística para gestión eficiente de paquetería',
          soon: true,
        },
        {
          icon: 'BusFront',
          titulo: 'Alquiler de Servicio',
          descripcion: 'Solución completa para renta y gestión de unidades',
          soon: true,
        },
      ],
      currentService: 0,
      serviceInterval: null,
      origen: '',
      destino: '',
      origenes: [],
      destinos: [],
      loadingOrigenes: true,
      errorOrigenes: null,
    };
  },
  computed: {
    destinosFiltrados() {
      if (!this.origen) return this.destinos;
      return this.destinos.filter(destino => destino !== this.origen);
    },
    // Computed para mostrar/ocultar el filtro de viajes según el rol
    showTravelFilter() {
      // Mostrar solo para client/passenger, no para cashier
      return this.authStore.hasRole('client') || 
             this.authStore.hasRole('passenger') ||
             (!this.authStore.hasRole('cashier') && !this.authStore.hasRole('admin') && !this.authStore.hasRole('super-admin'));
    },
    // Computed para el título dinámico de la sección
    sectionTitle() {
      if (this.authStore.hasRole('cashier') || 
          this.authStore.hasRole('admin') || 
          this.authStore.hasRole('super-admin')) {
        return 'Ventas Recientes';
      } else {
        return 'Viajes Recientes';
      }
    },
    // Computed para información de debug (opcional)
    userRoleInfo() {
      return {
        roles: this.authStore.roles,
        isCashier: this.authStore.hasRole('cashier'),
        isClient: this.authStore.hasRole('client'),
        isPassenger: this.authStore.hasRole('passenger'),
        showFilter: this.showTravelFilter,
        sectionTitle: this.sectionTitle
      };
    }
  },
  methods: {
    nextSlide() {
      this.currentSlide = (this.currentSlide + 1) % this.slides.length;
    },
    nextService() {
      this.currentService = (this.currentService + 1) % this.servicios.length;
    },
    goToService(index) {
      this.currentService = index;
      this.resetServiceInterval();
    },
    resetServiceInterval() {
      clearInterval(this.serviceInterval);
      this.serviceInterval = setInterval(this.nextService, 5000);
    },
    async fetchOrigenesDestinos() {
      // Solo cargar orígenes y destinos si el filtro está visible
      if (!this.showTravelFilter) {
        this.loadingOrigenes = false;
        return;
      }

      try {
        this.loadingOrigenes = true;
        const { data } = await api.get('/trips/');
        const viajes = data.map(viaje => ({
          origen: viaje.locality_start_name,
          destino: viaje.locality_end_name,
        }));
        const origenesSet = new Set(viajes.map(viaje => viaje.origen));
        const destinosSet = new Set(viajes.map(viaje => viaje.destino));
        this.origenes = [...origenesSet].sort();
        this.destinos = [...destinosSet].sort();
        console.log('Orígenes cargados:', this.origenes);
        console.log('Destinos cargados:', this.destinos);
      } catch (err) {
        this.errorOrigenes = 'Error al cargar los orígenes y destinos';
        console.error('Error en fetchOrigenesDestinos:', err);
      } finally {
        this.loadingOrigenes = false;
      }
    },
    aplicarFiltro() {
      console.log('Botón Buscar viajes clicado. Origen:', this.origen, 'Destino:', this.destino, 'Router disponible:', !!this.$router);
      if (!this.$router) {
        console.error('Vue Router no está disponible');
        return;
      }
      if (this.origen && this.destino) {
        try {
          this.$router.push({
            name: 'PuntoVenta',
            query: { origen: this.origen, destino: this.destino },
          });
          console.log('Navegando a /punto-venta con query:', { origen: this.origen, destino: this.destino });
        } catch (err) {
          console.error('Error al navegar:', err);
        }
      } else {
        console.warn('No se puede navegar: origen o destino no seleccionados');
      }
    },
    // Método para debug (opcional)
    logRoleInfo() {
      console.log('🔍 Información de roles en HomePage:');
      console.log('👤 Usuario:', this.authStore.user?.name);
      console.log('🎭 Roles:', this.authStore.roles);
      console.log('🔑 Permisos:', this.authStore.permissions);
      console.log('📋 Mostrar filtro de viajes:', this.showTravelFilter);
      console.log('🏷️  Título de sección:', this.sectionTitle);
      console.log('💳 Es cajero:', this.authStore.hasRole('cashier'));
      console.log('👥 Es cliente:', this.authStore.hasRole('client') || this.authStore.hasRole('passenger'));
    }
  },
  mounted() {
    this.slideInterval = setInterval(this.nextSlide, 5000);
    this.serviceInterval = setInterval(this.nextService, 5000);
    this.fetchOrigenesDestinos();
    
    // Log para debug (opcional)
    setTimeout(() => {
      this.logRoleInfo();
    }, 1000);
  },
  beforeUnmount() {
    clearInterval(this.slideInterval);
    clearInterval(this.serviceInterval);
  },
  watch: {
    // Observar cambios en los roles para actualizar la UI
    'authStore.roles': {
      handler() {
        console.log('🔄 Roles actualizados, recalculando UI...');
        this.fetchOrigenesDestinos(); // Recargar datos si es necesario
      },
      deep: true
    }
  }
};
</script>

<style scoped>
.home-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0;
}

.carousel-banner {
  width: 100vw;
  height: 250px;
  position: relative;
  overflow: hidden;
  margin-left: calc(50% - 50vw);
  margin-top: 0px;
}

.carousel-slide {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  opacity: 0;
  transition: opacity 0.5s ease-in-out;
}

.carousel-slide.active {
  opacity: 1;
}

.carousel-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.carousel-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
  color: #ffffff;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.carousel-text h2 {
  font-size: 2rem;
  margin-bottom: 8px;
  color: #e67e22;
}

.carousel-text p {
  font-size: 1rem;
  color: #ffffff;
}

.recent-trips-section {
  width: 100vw;
  padding: 100px 20px 60px;
  background-color: #f5f5f5;
  text-align: center;
  margin-left: calc(50% - 50vw);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  min-height: 300px;
}

.recent-trips-section h2 {
  font-size: 2rem;
  color: #333;
  font-weight: 600;
  margin-bottom: 100px;
  margin-top: -50px;
}

.app-banner {
  width: 100vw;
  padding: 40px 20px;
  background-color: #000000;
  color: #ffffff;
  text-align: center;
  margin-left: calc(50% - 50vw);
}

.app-banner h2 {
  font-size: 2rem;
  margin-bottom: 10px;
  color: #e67e22;
}

.app-banner p {
  font-size: 1.2rem;
  color: #ffffff;
}

.services-section {
  padding: 80px 0;
  position: relative;
  overflow: hidden;
}

.services-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
}

.section-title {
  text-align: center;
  font-size: 2.5rem;
  color: #2c3e50;
  margin-bottom: 15px;
  font-weight: 700;
  position: relative;
}

.section-title::after {
  content: '';
  display: block;
  width: 80px;
  height: 4px;
  background: #f39c12;
  margin: 15px auto 30px;
  border-radius: 2px;
}

.section-subtitle {
  text-align: center;
  font-size: 1.1rem;
  color: #7f8c8d;
  max-width: 700px;
  margin: 0 auto 50px;
  line-height: 1.6;
}

.services-carousel-wrapper {
  width: 100%;
  overflow: hidden;
  position: relative;
}

.services-carousel {
  display: flex;
  transition: transform 0.5s ease-in-out;
  width: 100%;
}

.service-card {
  flex: 0 0 100%;
  padding: 40px 30px;
  box-sizing: border-box;
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  border: 1px solid #eaeaea;
  margin: 0 10px;
  min-height: 300px;
}

.service-card:not(.coming-soon):hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  border-color: #f39c12;
}

.card-icon {
  width: 70px;
  height: 70px;
  background: linear-gradient(135deg, #f39c12, #e67e22);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 25px;
  color: white;
  font-size: 1.8rem;
  box-shadow: 0 5px 15px rgba(243, 156, 18, 0.3);
}

.service-card h3 {
  font-size: 1.5rem;
  color: #2c3e50;
  margin-bottom: 15px;
  font-weight: 600;
}

.service-card p {
  color: #7f8c8d;
  line-height: 1.6;
  margin-bottom: 25px;
  font-size: 1rem;
}

.card-footer {
  display: flex;
  align-items: center;
  color: #f39c12;
  font-weight: 500;
  transition: color 0.3s ease;
}

.service-card:not(.coming-soon) .card-footer {
  cursor: pointer;
}

.service-card:not(.coming-soon):hover .card-footer {
  color: #e67e22;
}

.card-footer i {
  margin-left: 8px;
  transition: transform 0.3s ease;
}

.service-card:not(.coming-soon):hover .card-footer i {
  transform: translateX(5px);
}

.coming-soon-badge {
  position: absolute;
  top: 20px;
  right: 20px;
  background: #e74c3c;
  color: white;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.card-highlight {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: #f39c12;
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.3s ease;
}

.service-card:not(.coming-soon):hover .card-highlight {
  transform: scaleX(1);
}

.coming-soon {
  opacity: 0.9;
  filter: grayscale(20%);
}

.carousel-dots {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.carousel-dots span {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #ddd;
  margin: 0 5px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.carousel-dots span.active {
  background: #f39c12;
}

.filtro-viajes {
  width: 100vw;
  padding: 40px 20px;
  background-color: #ffffff;
  text-align: center;
  margin-left: calc(50% - 50vw);
  margin-top: 20px;
}

.filtro-viajes h2 {
  font-size: 1.8rem;
  color: #2c3e50;
  font-weight: 600;
  margin-bottom: 20px;
}

.filtro-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 20px;
  max-width: 800px;
  margin: 0 auto;
}

.filtro-item {
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-width: 200px;
}

.filtro-item label {
  font-size: 1rem;
  color: #4a5568;
  font-weight: 500;
}

.filtro-item select {
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
  font-size: 1rem;
  color: #2d3748;
  background-color: #f7fafc;
  transition: border-color 0.3s ease;
}

.filtro-item select:focus {
  outline: none;
  border-color: #f6ad55;
  box-shadow: 0 0 0 2px rgba(246, 173, 85, 0.2);
}

.btn-filtrar {
  padding: 10px 24px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  background-color: #f6ad55;
  color: white;
  font-weight: 500;
  transition: all 0.2s ease;
  align-self: flex-end;
}

.btn-filtrar:hover {
  background-color: #ed8936;
  transform: translateY(-1px);
}

.btn-filtrar:disabled {
  background-color: #e2e8f0;
  cursor: not-allowed;
  opacity: 0.6;
}

.error {
  color: #e53e3e;
  font-size: 1rem;
  margin-top: 10px;
}

/* Estilos específicos para el modo cajero */
.cashier-mode .recent-trips-section {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.cashier-mode .recent-trips-section h2 {
  color: #28a745;
}

@media (max-width: 768px) {
  .carousel-banner {
    height: 200px;
    margin-top: -60px;
  }
  .carousel-text h2 {
    font-size: 1.5rem;
  }
  .carousel-text p {
    font-size: 0.9rem;
  }
  .recent-trips-section {
    padding: 60px 20px;
    margin-top: 20px;
  }
  .app-banner {
    padding: 30px 20px;
    margin-top: 20px;
  }
  .services-section {
    padding: 60px 0;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .service-card {
    padding: 30px 20px;
    min-height: 250px;
  }
  
  .card-icon {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
  }
  
  .filtro-container {
    flex-direction: column;
    align-items: center;
  }
  
  .btn-filtrar {
    width: 100%;
    max-width: 200px;
  }
}

@media (min-width: 768px) {
  .services-carousel {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    transform: none !important;
  }
  
  .service-card {
    flex: 1;
    min-width: auto;
  }
  
  .carousel-dots {
    display: none;
  }
}
</style>