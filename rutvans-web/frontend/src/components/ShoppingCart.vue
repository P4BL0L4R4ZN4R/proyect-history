<template>
  <div class="cart">
    <div class="cart-header">
      <h2>Resumen de viaje</h2>
      <div class="header-content">
        <img :src="viajeInfo.imagen || 'https://images.sipse.com/-EdvJzm23lm24Bjc7YwYmuLK1OI=/724x500/smart/2021/08/29/1630260268379.jpg'" alt="Viaje" class="header-image" />
        <div class="header-info">
          <p>{{ formattedDate }} - {{ formattedTime }} {{ viajeInfo.hora_llegada ? `- ${formattedArrivalTime}` : '' }}</p>
          <p><strong>Origen:</strong> {{ viajeInfo.origen || '' }}</p>
          <p><strong>Destino:</strong> {{ viajeInfo.destino || '' }}</p>
          <p v-if="selectedSeat"><strong>Asiento:</strong> {{ selectedSeat.seatNumber }}</p>
        </div>
      </div>
    </div>
    <div class="header-separator"></div>
    <div class="cart-body">
      <div class="cart-item">
        <p>Costo de viaje:</p>
        <p>${{ viajeInfo.precio_base || 0 }} MXN</p>
      </div>
      <div class="cart-item">
        <p>Subtotal:</p>
        <p>${{ viajeInfo.precio_base || 0 }} MXN</p>
      </div>
      <div class="cart-item">
        <p>Descuento:</p>
        <p>-${{ discount }} MXN</p>
      </div>
      <div class="body-separator"></div>
      <div class="cart-item total">
        <p>Total:</p>
        <p>${{ total }} MXN</p>
      </div>
    </div>
    <div class="cart-footer">
      <button :disabled="!isSeatSelected" @click="irAFormTicket" class="continue-btn">Continuar</button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

const props = defineProps({
  selectedFareType: Object,
  isSeatSelected: Boolean,
  selectedSeat: Object,
  viajeInfo: Object
});

const formattedDate = computed(() => {
  if (!props.viajeInfo.fecha) return 'No disponible';
  const [year, month, day] = props.viajeInfo.fecha.split('-');
  return `${day}/${month}/${year}`;
});

const formattedTime = computed(() => {
  if (!props.viajeInfo.hora_salida) return 'No disponible';
  return props.viajeInfo.hora_salida.slice(0, 5);
});

const formattedArrivalTime = computed(() => {
  if (!props.viajeInfo.hora_llegada) return 'No disponible';
  return props.viajeInfo.hora_llegada.slice(0, 5);
});

const discount = computed(() => {
  if (!props.selectedFareType || !props.viajeInfo.precio_base) return 0;
  return (props.viajeInfo.precio_base * (props.selectedFareType.percentage / 100)).toFixed(2);
});

const total = computed(() => {
  if (!props.viajeInfo.precio_base) return 0;
  return (props.viajeInfo.precio_base - discount.value).toFixed(2);
});

const irAFormTicket = () => {
  console.log('ShoppingCart.vue - company_name:', props.viajeInfo.company_name); // Depuración
  router.push({
    name: 'FormTicket',
    query: {
      routeUnitScheduleId: route.query.routeUnitScheduleId,
      type: props.selectedFareType?.name || 'Regular',
      base_price: props.viajeInfo.precio_base || 0,
      discount: discount.value,
      total_price: total.value,
      seatSelected: props.isSeatSelected,
      seatNumber: props.selectedSeat?.seatNumber || '',
      origen: props.viajeInfo.origen || 'No disponible',
      destino: props.viajeInfo.destino || 'No disponible',
      fecha: props.viajeInfo.fecha || '',
      hora: props.viajeInfo.hora_salida || '',
      hora_llegada: props.viajeInfo.hora_llegada || '',
      fechaF: formattedDate.value,
      horaF: formattedTime.value,
      hora_llegadaF: formattedArrivalTime.value,
      imagen: props.viajeInfo.imagen || '',
      companyName: props.viajeInfo.company_name || 'RUTVANS' // Nuevo parámetro
    }
  });
};
</script>

<style scoped>
.cart {
  background-color: #ffffff;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  min-height: 600px;
  display: flex;
  flex-direction: column;
}

.cart-header {
  background: linear-gradient(to right, #fff7ed, #fef5e3);
  border-radius: 10px 10px 0 0;
  padding: 20px;
  width: 100%;
  box-sizing: border-box;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.cart-header h2 {
  margin-bottom: 15px;
  font-size: 1.6rem;
  color: #f39c12;
  font-weight: 600;
  border-bottom: 2px solid #f39c12;
  padding-bottom: 5px;
}

.header-content {
  display: flex;
  align-items: flex-start;
  gap: 20px;
  padding: 10px 0;
}

.header-image {
  width: 130px;
  height: 100px;
  border-radius: 10px;
  object-fit: cover;
  box-shadow: 0 0 8px rgba(243, 156, 18, 0.3);
}

.header-info {
  flex: 1;
}

.header-info p {
  margin: 6px 0;
  font-size: 1rem;
  color: #333;
  line-height: 1.4;
}

.header-info p strong {
  color: #f39c12;
  font-weight: 600;
}

.header-separator {
  width: 100%;
  height: 1px;
  background-color: #ddd;
  margin-bottom: 10px;
}

.cart-body {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 20px;
  gap: 15px;
}

.cart-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
}

.cart-item p {
  margin: 0;
  font-size: 0.9rem;
  color: #020202;
}

.cart-item.total p {
  font-size: 1.1rem;
  font-weight: bold;
  color: #2c3e50;
}

.body-separator {
  width: 100%;
  height: 1px;
  background-color: #ddd;
  margin: 10px 0;
}

.cart-footer {
  padding: 20px;
  text-align: center;
  margin-top: 20px;
}

.continue-btn {
  background-color: #f39c12;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s;
}

.continue-btn:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.continue-btn:hover:not(:disabled) {
  background-color: #e67e22;
}
</style>