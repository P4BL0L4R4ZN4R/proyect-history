<template>
  <div class="max-w-6xl mx-auto p-6">
    <div class="bg-[#35495e] text-white rounded-2xl shadow-lg p-6">
      <div class="flex justify-between items-center border-b border-[#42b883] pb-4 mb-4">
        <h1 class="text-2xl font-bold">🕒 Administra los horarios</h1>
      </div>

      <div v-if="horarios.length > 0" class="overflow-x-auto">
        <table class="min-w-full table-auto text-sm">
          <thead class="bg-[#42b883] text-white">
            <tr>
              <th class="px-4 py-3 text-left">ID</th>
              <th class="px-4 py-3 text-left">Día</th>
              <th class="px-4 py-3 text-left">Hora de Llegada</th>
              <th class="px-4 py-3 text-left">Hora de Salida</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#2c3e50]">
            <tr
              v-for="horario in horarios"
              :key="horario.id"
              class="hover:bg-[#3e5872] transition duration-200"
            >
              <td class="px-4 py-2">{{ horario.id }}</td>
              <td class="px-4 py-2 capitalize">{{ horario.dia }}</td>
              <td class="px-4 py-2">{{ horario.horaLlegada }}</td>
              <td class="px-4 py-2">{{ horario.horaSalida }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="text-center text-gray-300 mt-6">
        <p>No hay horarios registrados.</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'Horarios',
  data() {
    return {
      horarios: [],
    }
  },
  mounted() {
    this.obtenerHorarios()
  },
  methods: {
    async obtenerHorarios() {
      try {
        const response = await axios.get('http://localhost:8000/api/horarios')
        this.horarios = response.data
      } catch (error) {
        console.error('Error al obtener los horarios:', error)
      }
    },
  },
}
</script>

<style scoped>
table {
  border-collapse: collapse;
}
</style>
