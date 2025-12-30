<template>
  <div class="viajes-disponibles">
    <div class="titulo-container">
      <h1>Viajes Disponibles</h1>
      <div class="filtro-bar" :class="{ 'filtro-bar-inactive': !hasActiveFilters }" @click="toggleFiltroPanel">
        <span class="filtro-texto">Filtro</span>
        <span v-if="hasActiveFilters" class="filtro-limpiar" @click.stop="limpiarFiltros">✕</span>
        <span v-else class="filtro-icono">☰</span>
      </div>
    </div>
    <div v-if="mostrarFiltroPanel" class="filtro-panel">
      <div class="filtro-campos">
        <div class="filtro-campo">
          <label for="origen">Origen</label>
          <select v-model="filtroOrigen" id="origen">
            <option value="">Selecciona un origen</option>
            <option v-for="origen in origenes" :key="origen" :value="origen">
              {{ origen }}
            </option>
          </select>
        </div>
        <div class="filtro-campo">
          <label for="destino">Destino</label>
          <select v-model="filtroDestino" id="destino" :disabled="!filtroOrigen">
            <option value="">Selecciona un destino</option>
            <option v-for="destino in destinosFiltrados" :key="destino" :value="destino">
              {{ destino }}
            </option>
          </select>
        </div>
      </div>
      <div class="filtro-acciones">
        <button @click="aplicarFiltros" class="btn-aplicar">Aplicar</button>
        <button @click="limpiarFiltros" class="btn-limpiar">Limpiar</button>
      </div>
    </div>
    <div v-if="loading" class="loading">Cargando viajes...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else-if="Object.keys(viajesPorTerminal).length === 0" class="no-viajes">
      No hay viajes disponibles para los criterios seleccionados
    </div>
    <div v-else class="terminales-container">
      <div v-for="(terminal, terminalName) in viajesPorTerminal" :key="terminalName" class="terminal-grupo">
        <h2 class="terminal-titulo">{{ terminalName }}</h2>
        <hr class="separador-terminal" />
        <div class="tarjetas-container">
          <div v-for="(viaje, index) in terminal.viajes" :key="index" class="tarjeta-container">
            <div :class="viaje.mostrarDetalles ? 'tarjeta-cambio' : 'tarjeta'">
              <img :src="viaje.imagen" alt="viaje" class="viaje-imagen" />
              <div class="viaje-info">
                <div class="ruta-info">
                  <div class="origen-destino">
                    <span class="lugar origen">{{ viaje.origen }}</span>
                    <div class="separador-ruta">
                      <img src="https://www.ado.com.mx/images/distance-icon.svg" alt="Ruta" class="icono-ruta" />
                    </div>
                    <span class="lugar destino">{{ viaje.destino }}</span>
                  </div>
                  <div class="hora-salida">
                    <i class="fas fa-clock icono-hora"></i>
                    <span>{{ viaje.horaSalida }}</span>
                  </div>
                </div>
              </div>
              <div class="viaje-acciones">
                <button @click="() => irASeleccionAsiento(viaje)" class="btn-escoger">
                  Escoger asiento
                </button>
                <span @click="toggleDetalles(terminalName, index)" class="texto-detalles">
                  {{ viaje.mostrarDetalles ? 'Ocultar detalles' : 'Ver detalles' }}
                </span>
                <p class="remaining-seats">Asientos restantes: {{ viaje.remainingSeats }}</p>
              </div>
            </div>
            <div v-if="viaje.mostrarDetalles" class="detalles-panel">
              <div class="detalles-item">
                <strong>Conductor:</strong> {{ viaje.conductor }}
              </div>
              <div class="detalles-item">
                <strong>Modelo:</strong> {{ viaje.modeloUnidad }}
              </div>
              <div class="detalles-item">
                <strong>Placas:</strong> {{ viaje.placas }}
              </div>
              <div class="detalles-item">
                <strong>Capacidad:</strong> {{ viaje.capacidad }} asientos
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/api';

export default {
  name: 'ViajesDisponibles',
  data() {
    return {
      viajes: [],
      origenes: [],
      destinos: [],
      loading: true,
      error: null,
      mostrarFiltroPanel: false,
      filtroOrigen: this.$route.query.origen?.trim() || '',
      filtroDestino: this.$route.query.destino?.trim() || '',
    };
  },
  computed: {
    destinosFiltrados() {
      if (!this.filtroOrigen) return this.destinos;
      return this.destinos.filter(destino => destino !== this.filtroOrigen);
    },
    viajesPorTerminal() {
      return this.viajes.reduce((acc, viaje) => {
        const terminal = viaje.site_name || 'No especificada';
        if (!acc[terminal]) {
          acc[terminal] = { viajes: [] };
        }
        acc[terminal].viajes.push(viaje);
        return acc;
      }, {});
    },
    hasActiveFilters() {
      return !!this.filtroOrigen || !!this.filtroDestino;
    },
  },
  methods: {
    async fetchViajes() {
      try {
        this.loading = true;
        const { data } = await api.get('/trips/');
        
        // Extraer orígenes y destinos únicos
        const origenesSet = new Set(data.map(v => v.locality_start_name));
        const destinosSet = new Set(data.map(v => v.locality_end_name));
        this.origenes = [...origenesSet].sort();
        this.destinos = [...destinosSet].sort();

        console.log('Orígenes extraídos:', this.origenes);
        console.log('Destinos extraídos:', this.destinos);
        console.log('Filtros aplicados:', { origen: this.filtroOrigen, destino: this.filtroDestino });

        this.viajes = await Promise.all(
          data
            .filter(viaje => {
              let coincide = true;
              if (this.filtroOrigen) {
                coincide = coincide && viaje.locality_start_name.trim() === this.filtroOrigen;
              }
              if (this.filtroDestino) {
                coincide = coincide && viaje.locality_end_name.trim() === this.filtroDestino;
              }
              return coincide;
            })
            .map(async viaje => {
              // Obtener asientos para calcular los restantes
              let remainingSeats = 0;
              try {
                const { data: asientos } = await api.get(`trips/seats/${viaje.route_unit_schedule_id}`);
                remainingSeats = asientos.filter(seat => seat.status === 'available').length;
              } catch (err) {
                console.error(`Error al obtener asientos para viaje ${viaje.route_unit_schedule_id}:`, err);
                remainingSeats = 'No disponible';
              }

              return {
                ...viaje,
                mostrarDetalles: false,
                origen: viaje.locality_start_name,
                destino: viaje.locality_end_name,
                conductor: viaje.driver_name,
                modeloUnidad: viaje.unit_model,
                placas: viaje.unit_plate,
                capacidad: viaje.unit_capacity,
                precio: viaje.price,
                horaSalida: viaje.hora_salida ? viaje.hora_salida.slice(0, 5) : 'No disponible',
                horaLlegada: viaje.arrival_time ? viaje.arrival_time.slice(0, 5) : 'No disponible',
                imagen: viaje.unit_photo ? `http://127.0.0.1:8000/storage/${viaje.unit_photo}` : 'https://images.sipse.com/-EdvJzm23lm24Bjc7YwYmuLK1OI=/724x500/smart/2021/08/29/1630260268379.jpg',
                site_name: viaje.site_name,
                remainingSeats
              };
            })
        );

        console.log('Viajes filtrados:', this.viajes);
      } catch (err) {
        this.error = 'Error al cargar los viajes disponibles';
        console.error('Error en fetchViajes:', err);
      } finally {
        this.loading = false;
      }
    },
    toggleFiltroPanel() {
      this.mostrarFiltroPanel = !this.mostrarFiltroPanel;
    },
    aplicarFiltros() {
      this.$router.push({
        name: 'PuntoVenta',
        query: {
          origen: this.filtroOrigen,
          destino: this.filtroDestino
        }
      });
      this.mostrarFiltroPanel = false;
    },
    limpiarFiltros() {
      this.filtroOrigen = '';
      this.filtroDestino = '';
      this.$router.push({
        name: 'PuntoVenta',
        query: {}
      });
      this.mostrarFiltroPanel = false;
    },
    irASeleccionAsiento(viaje) {
      this.$router.push({ 
        name: 'VentaAsientos',
        query: {
          routeUnitScheduleId: viaje.route_unit_schedule_id,
          origen: viaje.origen,
          destino: viaje.destino,
          fecha: viaje.fecha,
          hora_salida: viaje.horaSalida,
          hora_llegada: viaje.horaLlegada,
          precio_base: viaje.precio,
          imagen: viaje.imagen,
          site_name: viaje.site_name
        }
      });
    },
    toggleDetalles(terminalName, index) {
      this.viajesPorTerminal[terminalName].viajes[index].mostrarDetalles = !this.viajesPorTerminal[terminalName].viajes[index].mostrarDetalles;
    },
  },
  watch: {
    '$route.query': {
      handler(newQuery, oldQuery) {
        if (newQuery.origen !== oldQuery?.origen || newQuery.destino !== oldQuery?.destino) {
          this.filtroOrigen = newQuery.origen?.trim() || '';
          this.filtroDestino = newQuery.destino?.trim() || '';
          this.fetchViajes();
        }
      },
      deep: true,
      immediate: true
    }
  },
  mounted() {
    this.fetchViajes();
  },
};
</script>

<style scoped>
.loading {
  padding: 20px;
  text-align: center;
  color: #666;
  font-style: italic;
  font-size: 1.1rem;
}

.error {
  padding: 15px;
  text-align: center;
  background-color: #FFF5F5;
  color: #E53E3E;
  border-radius: 8px;
  margin: 20px auto;
  max-width: 600px;
  font-weight: 500;
  border-left: 4px solid #E53E3E;
}

.viajes-disponibles {
  padding: 0px;
  min-height: 100vh;
  width: 100%;
  position: relative;
}

.titulo-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  max-width: 800px;
  margin: 0 auto;
  padding: 20px 20px 30px;
  position: relative;
}

h1 {
  font-size: 1.8rem;
  color: #2D3748;
  font-weight: 600;
  position: relative;
  padding-bottom: 10px;
  text-align: center;
  flex: 1;
}

h1::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 3px;
  background: #F6AD55;
}

.filtro-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 12px;
  background-color: #F6AD55;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.filtro-bar.filtro-bar-inactive {
  background-color: #E2E8F0;
}

.filtro-bar.filtro-bar-inactive:hover {
  background-color: #CBD5E0;
}

.filtro-bar:hover {
  background-color: #ED8936;
}

.filtro-texto {
  font-size: 0.95rem;
  color: white;
  font-weight: 500;
}

.filtro-limpiar, .filtro-icono {
  font-size: 0.95rem;
  color: white;
  font-weight: 700;
  cursor: pointer;
  transition: color 0.2s ease;
}

.filtro-bar.filtro-bar-inactive .filtro-texto,
.filtro-bar.filtro-bar-inactive .filtro-icono {
  color: #4A5568;
}

.filtro-limpiar:hover,
.filtro-icono:hover {
  color: #FFE4B5;
}

.filtro-bar.filtro-bar-inactive .filtro-icono:hover {
  color: #2D3748;
}

.filtro-panel {
  position: absolute;
  top: 90px;
  right: 20px;
  background-color: #FFFFFF;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  padding: 20px;
  max-width: 400px;
  width: 90%;
  z-index: 20;
  backdrop-filter: blur(5px);
  border: 1px solid #E2E8F0;
}

.filtro-campos {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.filtro-campo {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.filtro-campo label {
  font-size: 0.95rem;
  font-weight: 500;
  color: #2D3748;
}

.filtro-campo select {
  padding: 8px;
  border: 1px solid #E2E8F0;
  border-radius: 6px;
  font-size: 0.95rem;
  color: #4A5568;
  background-color: #F7FAFC;
}

.filtro-campo select:disabled {
  background-color: #E2E8F0;
  cursor: not-allowed;
}

.filtro-acciones {
  display: flex;
  gap: 10px;
  margin-top: 15px;
  justify-content: flex-end;
}

.btn-aplicar, .btn-limpiar {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.95rem;
  font-weight: 500;
  transition: all 0.2s ease;
}

.btn-aplicar {
  background-color: #F6AD55;
  color: white;
}

.btn-aplicar:hover {
  background-color: #ED8936;
}

.btn-limpiar {
  background-color: #E2E8F0;
  color: #4A5568;
}

.btn-limpiar:hover {
  background-color: #CBD5E0;
}

.terminales-container {
  display: flex;
  flex-direction: column;
  gap: 40px;
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
  padding: 0 20px 40px;
}

.terminal-grupo {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.terminal-titulo {
  font-size: 1.5rem;
  font-weight: 600;
  color: #2D3748;
  text-align: right;
  margin-bottom: 5px;
}

.separador-terminal {
  border: none;
  height: 2px;
  background-color: #000000;
  margin: 0;
  width: 95%;
  align-self: flex-end;
}

.tarjetas-container {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.tarjeta-container {
  display: flex;
  flex-direction: column;
  width: 100%;
  transition: all 0.3s ease;
}

.tarjeta {
  background-color: #FFFFFF;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  padding: 25px;
  display: flex;
  align-items: center;
  gap: 25px;
  min-height: 120px;
  transition: all 0.3s ease;
  border: 1px solid #EDF2F7;
}

.tarjeta:hover {
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.tarjeta-cambio {
  background-color: #FFFFFF;
  border-radius: 12px 12px 0 0;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  padding: 25px;
  display: flex;
  align-items: center;
  gap: 25px;
  min-height: 120px;
  border: 1px solid #EDF2F7;
  border-bottom: none;
}

.viaje-imagen {
  width: 160px;
  height: 110px;
  border-radius: 8px;
  object-fit: cover;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.viaje-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.ruta-info {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.origen-destino {
  display: flex;
  align-items: center;
  gap: 10px;
}

.lugar {
  font-size: 1.1rem;
  font-weight: 500;
  color: #2D3748;
}

.lugar.origen {
  text-align: right;
  flex: 1;
}

.lugar.destino {
  text-align: left;
  flex: 1;
}

.separador-ruta {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.icono-ruta {
  width: 20px;
  height: auto;
  opacity: 0.7;
}

.hora-salida {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: #F7FAFC;
  padding: 6px 12px;
  border-radius: 20px;
  width: fit-content;
  margin: 0 auto;
}

.icono-hora {
  color: #F6AD55;
  font-size: 0.9rem;
}

.hora-salida span {
  font-size: 0.95rem;
  font-weight: 500;
  color: #4A5568;
}

.viaje-acciones {
  display: flex;
  flex-direction: column;
  gap: 12px;
  align-items: flex-end;
}

.btn-escoger {
  padding: 10px 24px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.95rem;
  background-color: #F6AD55;
  color: white;
  font-weight: 500;
  transition: all 0.2s ease;
  box-shadow: 0 2px 4px rgba(246, 173, 85, 0.3);
}

.btn-escoger:hover {
  background-color: #ED8936;
  transform: translateY(-1px);
  box-shadow: 0 4px 6px rgba(246, 173, 85, 0.4);
}

.texto-detalles {
  text-decoration: none;
  color: #718096;
  cursor: pointer;
  font-size: 0.9rem;
  text-align: right;
  transition: all 0.2s ease;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 5px;
}

.texto-detalles:hover {
  color: #2D3748;
}

.texto-detalles::after {
  transition: transform 0.2s ease;
}

.texto-detalles:hover::after {
  transform: translateX(3px);
}

.remaining-seats {
  font-size: 0.9rem;
  color: #666;
  margin: 5px 0 0;
  text-align: right;
}

.detalles-panel {
  width: 100%;
  padding: 20px;
  background-color: #FFFFFF;
  border-radius: 0 0 12px 12px;
  font-size: 0.95rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #EDF2F7;
  border-top: none;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 15px;
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.detalles-item {
  padding: 8px 12px;
  background-color: #F7FAFC;
  border-radius: 6px;
  color: #4A5568;
  font-weight: 500;
}

.detalles-item strong {
  color: #2D3748;
  font-weight: 600;
}

.no-viajes {
  padding: 25px;
  text-align: center;
  background-color: #FFFFFF;
  color: #4A5568;
  border-radius: 12px;
  margin: 30px auto;
  max-width: 500px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #E2E8F0;
  font-size: 1.1rem;
}

@media (max-width: 768px) {
  .tarjeta, .tarjeta-cambio {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }
  
  .viaje-imagen {
    width: 100%;
    height: 150px;
  }
  
  .viaje-acciones {
    align-items: center;
    width: 100%;
  }
  
  .origen-destino {
    flex-direction: column;
    gap: 5px;
  }
  
  .lugar.origen,
  .lugar.destino {
    text-align: center;
  }
  
  .separador-ruta {
    transform: rotate(90deg);
    padding: 8px 0;
  }
  
  .detalles-panel {
    grid-template-columns: 1fr;
  }
  
  .terminal-titulo {
    text-align: center;
  }
  
  .separador-terminal {
    width: 100%;
    align-self: center;
  }
  
  .filtro-panel {
    max-width: 95%;
    top: 110px;
    right: 10px;
  }
  
  .titulo-container {
    flex-direction: column;
    gap: 10px;
    align-items: center;
  }
  
  .filtro-bar {
    align-self: flex-end;
  }
  
  .remaining-seats {
    text-align: center;
  }
}
</style>