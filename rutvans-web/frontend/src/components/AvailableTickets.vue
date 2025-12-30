<template>
  <div class="available-tickets">
    <div class="ticket-card">
      <div class="ticket-image-container">
        <img :src="viajeInfo.imagen" alt="Asiento" class="ticket-image" />
      </div>
      <div class="separator"></div>
      <div class="ticket-info">
        <p class="passenger-label">Pasajero -</p>
        <select v-model="selectedFareType" class="passenger-select" @change="handleFareTypeChange">
          <option v-for="tarifa in fareTypes" :key="tarifa.id" :value="tarifa">
            {{ tarifa.name }} ({{ tarifa.percentage }}%)
          </option>
        </select>
      </div>
      <div class="ticket-price-container">
        <p class="ticket-price">Precio ${{ currentPrice }} MXN</p>
        <p class="remaining-seats">Asientos restantes: {{ remainingSeats }}</p>
      </div>
    </div>
    <ProgressBar :currentStep="currentStep" :progress="progress"/>
    <div class="seat-layout">
      <h2>Selecciona tu asiento</h2>
      <div v-if="loading" class="loading">Cargando asientos disponibles...</div>
      <div v-else-if="error" class="error">{{ error }}</div>
      <div v-else class="seat-map">
        <div v-for="seat in seats" :key="seat.id">
          <div v-if="seat.seatNumber.trim() !== ''" 
            :class="['seat', seat.status, { selected: seat.selected }]" 
            @click="selectSeat(seat)"
          >
            <div class="seat-bottom">
              <div class="arm"></div>
              <div class="cushion">
                <span class="seat-number">{{ seat.seatNumber }}</span>
              </div>
              <div class="arm"></div>
            </div>
            <div class="backrest"></div>
          </div>
          <div v-else class="empty-space"></div>
        </div>
      </div>
      <div class="legend">
        <div class="legend-item">
          <div class="seat selected"></div>
          <span>Seleccionado</span>
        </div>
        <div class="legend-item">
          <div class="seat occupied"></div>
          <span>Ocupado</span>
        </div>
        <div class="legend-item">
          <div class="seat available"></div>
          <span>Disponible</span>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
  import { ref, computed, onMounted, watch } from 'vue';
  import { useRoute } from 'vue-router';
  import ProgressBar from '@/components/ProgressBar.vue';
  import api from '@/api';

  const props = defineProps({
    viajeInfo: Object
  });
  const emit = defineEmits(['fare-type-changed', 'seat-selected']);
  

  const route = useRoute();
  const selectedFareType = ref(null);
  const seats = ref([]);
  const fareTypes = ref([]);
  const loading = ref(false);
  const error = ref(null);
  const currentStep = ref(1);
  const localSeatSelected = ref(false);


  const progress = computed(() => {
    let prog = 25;
    if (localSeatSelected.value || props.isSeatSelected) prog += 25;
    return prog;
  });

  watch(() => localSeatSelected.value || props.isSeatSelected, (isSelected) => {
  currentStep.value = isSelected ? 2 : 1;
});


  const fetchFareTypes = async () => {
    try {
      const { data } = await api.get('/trips/rates-type');
      fareTypes.value = data;
      if (data.length > 0) {
        selectedFareType.value = data[0];
      }
    } catch (err) {
      console.error('Error al obtener tipos de tarifas:', err);
      error.value = 'Error al cargar tipos de pasajeros';
    }
  };

  const fetchAsientos = async (routeUnitScheduleId) => {
    try {
      loading.value = true;
      const { data: asientos } = await api.get(`trips/seats/${routeUnitScheduleId}`);
      seats.value = asientos;
    } catch (err) {
      console.error('Error al obtener asientos:', err);
      error.value = 'No se pudieron cargar los asientos disponibles';
    } finally {
      loading.value = false;
    }
  };

  const currentPrice = computed(() => {
    if (!selectedFareType.value || !props.viajeInfo.precio_base) return 0;
    const discount = props.viajeInfo.precio_base * (selectedFareType.value.percentage / 100);
    return (props.viajeInfo.precio_base - discount).toFixed(2);
  });

  const remainingSeats = computed(() => {
    return seats.value.filter(seat => seat.status === 'available').length;
  });

  const selectSeat = (seat) => {
    if (seat.status === 'available') {
      seats.value.forEach(s => s.selected = false);
      seat.selected = true;
      emit('seat-selected', {
        seat,
        fareType: selectedFareType.value,
        viajeInfo: {
          ...props.viajeInfo,
          precio_base: props.viajeInfo.precio_base,
          origen: props.viajeInfo.origen,
          destino: props.viajeInfo.destino,
          fecha: props.viajeInfo.fecha,
          hora_salida: props.viajeInfo.hora_salida,
          hora_llegada: props.viajeInfo.hora_llegada,
          imagen: props.viajeInfo.imagen,
          company_name: props.viajeInfo.company_name // Nuevo campo
        }
      });
    }
  };

  const handleFareTypeChange = () => {
    emit('fare-type-changed', {
      type: selectedFareType.value,
      price: currentPrice.value
    });
  };

  onMounted(() => {
    fetchFareTypes();
    if (route.query.routeUnitScheduleId) {
      fetchAsientos(route.query.routeUnitScheduleId);
    }
  });
</script>
<style scoped>
  .available-tickets {
    padding: 20px;
  }
  .ticket-card {
    display: flex;
    align-items: center;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    gap: 20px;
  }
  .ticket-image-container {
    width: 150px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
  }
  .ticket-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }
  .separator {
    width: 1px;
    height: 80px;
    background-color: #ddd;
  }
  .ticket-info {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .passenger-label {
    font-size: 1rem;
    color: #2c3e50;
    margin: 0;
  }
  .passenger-select {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    color: #2c3e50;
    background-color: #fff;
    cursor: pointer;
    min-width: 150px;
  }
  .ticket-price-container {
    margin-left: auto;
    text-align: right;
  }
  .ticket-price {
    font-size: 1.25rem;
    font-weight: bold;
    color: #f39c12;
    margin: 0;
  }
  .remaining-seats {
    font-size: 0.9rem;
    color: #666;
    margin: 5px 0 0;
  }
  .seat-layout {
    margin-top: 30px;
    text-align: center;
  }
  .seat-map {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    justify-content: center;
    margin-top: 20px;
    background-image: url("../assets/vanfondo.png");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    min-height: 350px;
    padding: 60px 6% 60px 28%;
  }
  .seat {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    cursor: pointer;
    margin-left: 15px;
    border: 2px solid #ccc;
    transition: all 0.2s ease;
  }
  .seat-bottom {
    display: flex;
    flex-direction: column;
  }
  .arm {
    width: 30px;
    height: 10px;
    border-radius: 3px;
    border: 2px solid #ccc;
    align-self: flex-end;
  }
  .cushion {
    margin: 6px 0 6px -8px;
  }
  .backrest {
    width: 10px;
    height: 46px;
    border-radius: 3px;
    border: 2px solid #ccc;
    margin-right: -16px;
  }
  .empty-space {
    width: 50px;
    height: 50px;
    margin-left: 15px;
    visibility: hidden;
  }
  .available,
  .available .arm,
  .available .backrest {
    background-color: #e0e0e0;
  }
  .available:hover,
  .available:hover .arm,
  .available:hover .backrest {
    background-color: #bbbbbb;
  }
  .occupied,
  .occupied .arm,
  .occupied .backrest {
    background-color: #9e9e9e;
    cursor: not-allowed;
  }
  .selected,
  .selected .arm,
  .selected .backrest {
    background-color: #f39c12;
    color: white;
    border-color: #e67e22;
  }
  .selected:hover,
  .selected:hover .arm,
  .selected:hover .backrest {
    background-color: #ff7e0e;
  }
  .legend {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    gap: 15px;
  }
  .legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.9rem;
  }
  .legend-item .seat {
    width: 20px;
    height: 20px;
  }
  .loading {
    padding: 20px;
    text-align: center;
    color: #666;
    font-style: italic;
  }
  .error {
    padding: 15px;
    text-align: center;
    background-color: #ffeeee;
    color: #f83030;
    border-radius: 5px;
    margin: 10px 0;
    font-weight: bold;
  }
</style>