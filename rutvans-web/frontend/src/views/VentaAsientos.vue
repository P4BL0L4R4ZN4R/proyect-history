<template>
  <div class="punto-venta">
    <div class="contenido-punto-venta">
      <div class="boletos-section">
        <AvailableTickets
          :viajeInfo="viajeInfo"
          @fare-type-changed="handleFareTypeChange"
          @seat-selected="handleSeatSelected"
        />
      </div>
      <div class="carrito-section">
        <ShoppingCart
          :selectedFareType="selectedFareType"
          :isSeatSelected="isSeatSelected"
          :selectedSeat="selectedSeat"
          :viajeInfo="viajeInfo"
        />
      </div>
    </div>
  </div>
</template>
<script setup>
  import { ref, onMounted } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import AvailableTickets from '@/components/AvailableTickets.vue';
  import ShoppingCart from '@/components/ShoppingCart.vue';
  import api from '@/api';

  const route = useRoute();
  const router = useRouter();
  const selectedFareType = ref(null);
  const isSeatSelected = ref(false);
  const selectedSeat = ref(null);
  const viajeInfo = ref({
    origen: '',
    destino: '',
    fecha: '',
    hora_salida: '',
    hora_llegada: '',
    precio_base: 0,
    imagen: '',
    company_name: '' // Nuevo campo
  });

  const handleFareTypeChange = ({ type, price }) => {
    selectedFareType.value = type;
  };

  const handleSeatSelected = ({ seat, fareType, viajeInfo: info }) => {
    isSeatSelected.value = true;
    selectedSeat.value = seat;
    selectedFareType.value = fareType;
    viajeInfo.value = info;
    router.push({
      name: 'Ticket',
      query: {
        routeUnitScheduleId: route.query.routeUnitScheduleId,
        type: fareType.name,
        base_price: info.precio_base,
        discount: (info.precio_base - (info.precio_base * (1 - fareType.percentage / 100))).toFixed(2),
        total_price: (info.precio_base * (1 - fareType.percentage / 100)).toFixed(2),
        seatNumber: seat.seatNumber,
        origen: info.origen,
        destino: info.destino,
        fecha: info.fecha,
        hora: info.hora_salida,
        fechaF: info.fecha,
        horaF: info.hora_llegada,
        imagen: info.imagen,
        companyName: info.company_name // Nuevo parámetro
      }
    });
  };

  onMounted(async () => {
    if (route.query.routeUnitScheduleId) {
      try {
        const { data } = await api.get(`/trips/trip-info/${route.query.routeUnitScheduleId}`);
        viajeInfo.value = {
          origen: route.query.origen || data.origen,
          destino: route.query.destino || data.destino,
          fecha: route.query.fecha || data.fecha,
          hora_salida: route.query.hora_salida || data.hora_salida,
          hora_llegada: route.query.hora_llegada || data.hora_llegada,
          precio_base: Number(route.query.precio_base) || data.precio_base,
          imagen: route.query.imagen || data.imagen,
          company_name: route.query.companyName || data.company_name || 'RUTVANS' // Nuevo campo con fallback
        };
      } catch (error) {
        console.error('Error al obtener info del viaje:', error);
        viajeInfo.value = {
          origen: route.query.origen || '',
          destino: route.query.destino || '',
          fecha: route.query.fecha || '',
          hora_salida: route.query.hora_salida || '',
          hora_llegada: route.query.hora_llegada || '',
          precio_base: Number(route.query.precio_base) || 0,
          imagen: route.query.imagen || '',
          company_name: route.query.companyName || 'RUTVANS' // Fallback
        };
      }
    }
  });
</script>
<style scoped>
  .punto-venta {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    color: #2c3e50;
    display: flex;
    flex-direction: column;
    padding: 20px;
    background-color: #f8f9fa;
    min-height: 100vh;
    width: 100%;
  }
  .contenido-punto-venta {
    display: flex;
    gap: 40px;
    max-width: 1400px;
    margin: 0 auto;
    width: 100%;
    padding: 0 20px;
  }
  .boletos-section {
    flex: 3;
    max-width: 900px;
  }
  .carrito-section {
    flex: 1;
    max-width: 400px;
  }
  .titulo-boletos {
    text-align: left;
    margin-bottom: 10px;
    font-size: 1.5rem;
    color: #2c3e50;
    text-transform: uppercase;
  }
  .separador {
    width: 100%;
    height: 2px;
    background-color: #f39c12;
    margin-bottom: 20px;
  }
</style>