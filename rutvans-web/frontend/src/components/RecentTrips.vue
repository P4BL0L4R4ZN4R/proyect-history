<template>
  <div class="recent-trips">
    <div v-if="trips.length === 0">No tienes viajes recientes.</div>

    <div
      v-for="trip in trips"
      :key="trip.id"
      class="trip-card"
    >
      <div class="trip-summary">
        <div><strong>{{ trip.origin }}</strong> → <strong>{{ trip.destination }}</strong></div>
        <div>${{ trip.amount }} MXN</div>
        <button @click="toggleDetails(trip)">Detalles</button>
      </div>

      <div v-if="selectedTrip === trip.id" class="trip-details">
        <h4>Detalles del viaje</h4>
        <p><strong>Origen:</strong> {{ trip.origin }}</p>
        <p><strong>Destino:</strong> {{ trip.destination }}</p>
        <p><strong>Fecha:</strong> {{ trip.date }}</p>
        <p><strong>Hora:</strong> {{ trip.time }}</p>
        <p><strong>Conductor:</strong> {{ trip.driver }}</p>
        <p><strong>Monto:</strong> ${{ trip.amount }} MXN</p>
      </div>
    </div>
  </div>
</template>

<script>

import axios from 'axios';
export default {
  name: 'RecentTrips',
  data() {
    return {
      trips: [],
      selectedTrip: null,
    };
  },
  mounted() {
    this.fetchTrips();
  },
  methods: {
    fetchTrips() {
  axios.get('/cashier/trips/recent-trips', {
    headers: {
      'Authorization': `Bearer ${localStorage.getItem('token')}`
    }
  })
  .then(response => {
    this.trips = response.data.data;
  })
  .catch(error => {
    console.error('Error:', error);
    this.trips = [];
  });
},
    toggleDetails(trip) {
      this.selectedTrip = this.selectedTrip === trip.id ? null : trip.id;
    },
  },
};
</script>

<style scoped>
.recent-trips {
  width: 100%;
  max-width: 800px;
}

.trip-card {
  background: #fff;
  border: 1px solid #ddd;
  margin-bottom: 15px;
  border-radius: 8px;
  padding: 15px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.trip-summary {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.trip-summary button {
  background-color: #e67e22;
  color: white;
  border: none;
  padding: 6px 10px;
  border-radius: 4px;
  cursor: pointer;
}

.trip-summary button:hover {
  background-color: #d35400;
}

.trip-details {
  margin-top: 10px;
  background: #f9f9f9;
  padding: 10px;
  border-radius: 5px;
}
</style>
