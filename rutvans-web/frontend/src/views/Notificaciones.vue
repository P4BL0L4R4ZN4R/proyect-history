<template>
  <div class="notifications-container">
    <header class="notifications-header">
      <button class="back-button" @click="goBack">
        <ArrowLeft class="icon" />
      </button>
      <h1>Notificaciones</h1>
    </header>

    <!-- Filtros -->
    <div class="filters-section">
      <div class="filter-group">
        <label for="categoryFilter">Filtrar por categoría:</label>
        <select 
          id="categoryFilter" 
          v-model="filters.category" 
          class="filter-select"
        >
          <option value="">Todas las categorías</option>
          <option value="pago">Pagos</option>
          <option value="envio">Envíos</option>
          <option value="horario">Horarios</option>
          <option value="sistema">Sistema</option>
        </select>
      </div>

      <div class="filter-group">
        <label for="dateFilter">Filtrar por fecha:</label>
        <select 
          id="dateFilter" 
          v-model="filters.dateRange" 
          class="filter-select"
        >
          <option value="">Todas las fechas</option>
          <option value="today">Hoy</option>
          <option value="yesterday">Ayer</option>
          <option value="week">Esta semana</option>
          <option value="month">Este mes</option>
          <option value="custom">Personalizado</option>
        </select>
      </div>

      <!-- Fechas personalizadas -->
      <div v-if="filters.dateRange === 'custom'" class="custom-dates">
        <div class="date-input-group">
          <label for="startDate">Desde:</label>
          <input 
            id="startDate"
            type="date" 
            v-model="filters.startDate" 
            class="date-input"
          >
        </div>
        <div class="date-input-group">
          <label for="endDate">Hasta:</label>
          <input 
            id="endDate"
            type="date" 
            v-model="filters.endDate" 
            class="date-input"
          >
        </div>
      </div>

      <button class="clear-filters" @click="clearFilters">
        Limpiar filtros
      </button>
    </div>

    <main class="notifications-content">
      <div v-if="loading" class="loading-state">
        <p>Cargando notificaciones...</p>
      </div>

      <div v-else-if="filteredNotifications.length === 0" class="empty-state">
        <p>{{ hasFilters ? 'No hay notificaciones que coincidan con los filtros' : 'No tienes notificaciones' }}</p>
        <button v-if="hasFilters" @click="clearFilters" class="clear-filters-btn">
          Limpiar filtros
        </button>
      </div>

      <div
        class="notification-item"
        v-for="(notification, index) in filteredNotifications"
        :key="notification.id || index"
        @click="handleNotificationClick(notification)"
        :class="{ 'unread': isUnread(notification), 'read': !isUnread(notification) }"
      >
        <div class="notification-icon" :class="getNotificationCategory(notification)">
          <component :is="iconMap[getNotificationCategory(notification)]" class="icon" />
        </div>
        <div class="notification-details">
          <h3 :class="{ 'unread-title': isUnread(notification) }">
            {{ getNotificationTitle(notification) }}
          </h3>
          <p :class="{ 'unread-message': isUnread(notification) }">
            {{ notification.description || notification.message }}
          </p>
          <span class="notification-time">{{ formatTime(notification.created_at) }}</span>
          <span class="notification-category">{{ getNotificationCategory(notification) }}</span>
        </div>
        <div v-if="isUnread(notification)" class="unread-indicator"></div>
        <div v-else class="read-indicator">
          <CheckCircle class="icon-small" />
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { 
  Info, 
  AlertTriangle, 
  CheckCircle, 
  ArrowLeft,
  DollarSign,
  Truck,
  Clock,
  Calendar
} from 'lucide-vue-next'
import api from '@/api'

// Estado reactivo
const notifications = ref([])
const loading = ref(false)
const error = ref(null)

// Filtros
const filters = ref({
  category: '',
  dateRange: '',
  startDate: '',
  endDate: ''
})

// Mapeo de iconos por categoría
const iconMap = {
  pago: DollarSign,
  envio: Truck,
  horario: Clock,
  sistema: Info,
  default: Info
}

// Computed: Notificaciones filtradas
const filteredNotifications = computed(() => {
  let filtered = notifications.value

  // Filtrar por categoría
  if (filters.value.category) {
    filtered = filtered.filter(notification => 
      getNotificationCategory(notification) === filters.value.category
    )
  }

  // Filtrar por fecha
  if (filters.value.dateRange) {
    filtered = filtered.filter(notification => {
      const notificationDate = new Date(notification.created_at)
      return filterByDateRange(notificationDate, filters.value.dateRange)
    })
  }

  return filtered
})

// Computed: Verificar si hay filtros activos
const hasFilters = computed(() => {
  return filters.value.category !== '' || filters.value.dateRange !== ''
})

// Función para filtrar por rango de fechas
const filterByDateRange = (date, range) => {
  const today = new Date()
  const startOfDay = new Date(today.getFullYear(), today.getMonth(), today.getDate())
  const startOfYesterday = new Date(startOfDay)
  startOfYesterday.setDate(startOfYesterday.getDate() - 1)
  
  const startOfWeek = new Date(today)
  startOfWeek.setDate(today.getDate() - today.getDay())
  startOfWeek.setHours(0, 0, 0, 0)
  
  const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1)

  switch (range) {
    case 'today':
      return date >= startOfDay
    case 'yesterday':
      return date >= startOfYesterday && date < startOfDay
    case 'week':
      return date >= startOfWeek
    case 'month':
      return date >= startOfMonth
    case 'custom':
      if (filters.value.startDate && filters.value.endDate) {
        const start = new Date(filters.value.startDate)
        const end = new Date(filters.value.endDate)
        end.setHours(23, 59, 59, 999) // Incluir todo el día final
        return date >= start && date <= end
      }
      return true
    default:
      return true
  }
}

// Limpiar filtros
const clearFilters = () => {
  filters.value = {
    category: '',
    dateRange: '',
    startDate: '',
    endDate: ''
  }
}

// Función para cargar notificaciones desde el API
const loadNotifications = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await api.get('/notifications-data')
    notifications.value = response.data.data || []
  } catch (err) {
    console.error('Error al cargar notificaciones:', err)
    error.value = 'No se pudieron cargar las notificaciones'
    notifications.value = getFallbackNotifications()
  } finally {
    loading.value = false
  }
}

// Determinar si una notificación no ha sido leída
const isUnread = (notification) => {
  return notification.status === 'notread'
}

// Obtener la categoría de la notificación
const getNotificationCategory = (notification) => {
  return notification.category || 'default'
}

// Generar título basado en la categoría
const getNotificationTitle = (notification) => {
  const category = getNotificationCategory(notification)
  
  const titleMap = {
    pago: 'Información de Pago',
    envio: 'Estado de Envío',
    horario: 'Cambio de Horario',
    sistema: 'Notificación del Sistema',
    default: 'Notificación'
  }
  
  return titleMap[category] || 'Notificación'
}

// Función para formatear el tiempo
const formatTime = (timestamp) => {
  if (!timestamp) return 'Reciente'
  
  try {
    const now = new Date()
    const notificationTime = new Date(timestamp)
    const diffInMinutes = Math.floor((now - notificationTime) / (1000 * 60))
    const diffInHours = Math.floor(diffInMinutes / 60)
    const diffInDays = Math.floor(diffInHours / 24)
    
    if (diffInMinutes < 1) return 'Ahora mismo'
    if (diffInMinutes < 60) return `Hace ${diffInMinutes} minuto${diffInMinutes > 1 ? 's' : ''}`
    if (diffInHours < 24) return `Hace ${diffInHours} hora${diffInHours > 1 ? 's' : ''}`
    if (diffInDays < 7) return `Hace ${diffInDays} día${diffInDays > 1 ? 's' : ''}`
    
    return notificationTime.toLocaleDateString('es-ES', {
      day: 'numeric',
      month: 'short',
      year: 'numeric'
    })
  } catch {
    return 'Reciente'
  }
}

// Manejar clic en notificación
const handleNotificationClick = async (notification) => {
  try {
    if (isUnread(notification)) {
      await api.put(`/notifications/${notification.id}/read`)
      notification.status = 'read'
    }
  } catch (err) {
    console.error('Error al actualizar notificación:', err)
  }
}

// Datos de fallback en caso de error
const getFallbackNotifications = () => {
  return [
    {
      id: 1,
      message: "Horario modificado para la ruta 10",
      status: "notread",
      category: "horario",
      created_at: new Date().toISOString()
    },
    {
      id: 2,
      message: "Pago procesado correctamente",
      status: "read", 
      category: "pago",
      created_at: new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString() // Ayer
    },
    {
      id: 3,
      message: "Envío en camino a su destino",
      status: "notread",
      category: "envio",
      created_at: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString() // Hace 2 días
    }
  ]
}

// Navegación
const goBack = () => {
  window.history.back()
}

// Cargar notificaciones al montar el componente
onMounted(() => {
  loadNotifications()
})
</script>

<style scoped>
.notifications-container {
  height: 100vh;
  display: flex;
  flex-direction: column;
  margin: 0;
  padding: 0;
}

.notifications-header {
  display: flex;
  align-items: center;
  padding: 16px;
  background-color: white;
  border-bottom: 1px solid #eaeaea;
  position: sticky;
  top: 0;
  z-index: 10;
  width: 100%;
  margin: 0;
  box-sizing: border-box;
}

.back-button {
  background: none;
  border: none;
  font-size: 1.2rem;
  margin-right: 15px;
  color: var(--primary-color);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px;
}

/* Estilos para filtros */
.filters-section {
  padding: 15px;
  background-color: #f8f9fa;
  border-bottom: 1px solid #eaeaea;
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  align-items: end;
}

.filter-group {
  display: flex;
  flex-direction: column;
  min-width: 150px;
}

.filter-group label {
  font-size: 0.8rem;
  color: #666;
  margin-bottom: 5px;
  font-weight: 500;
}

.filter-select, .date-input {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 0.9rem;
  background-color: white;
}

.filter-select:focus, .date-input:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.custom-dates {
  display: flex;
  gap: 10px;
  align-items: end;
}

.date-input-group {
  display: flex;
  flex-direction: column;
}

.date-input-group label {
  font-size: 0.8rem;
  color: #666;
  margin-bottom: 5px;
  font-weight: 500;
}

.clear-filters {
  padding: 8px 16px;
  background-color: #95a5a6;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9rem;
  height: fit-content;
}

.clear-filters:hover {
  background-color: #7f8c8d;
}

.clear-filters-btn {
  padding: 8px 16px;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 10px;
}

.clear-filters-btn:hover {
  background-color: #2980b9;
}

.notifications-content {
  flex: 1;
  overflow-y: auto;
  padding: 15px;
  width: 100%;
  box-sizing: border-box;
}

/* Estilos para notificaciones NO LEÍDAS */
.notification-item.unread {
  background: #f8fbff;
  border-left: 4px solid #3498db;
  border: 1px solid #e1f0fa;
}

.notification-item.unread:hover {
  background: #f0f7ff;
  box-shadow: 0 4px 12px rgba(52, 152, 219, 0.15);
}

.unread-title {
  color: #2c3e50;
  font-weight: 600;
}

.unread-message {
  color: #34495e;
  font-weight: 500;
}

.unread-indicator {
  position: absolute;
  top: 15px;
  right: 15px;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background-color: #3498db;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { transform: scale(0.95); opacity: 0.8; }
  50% { transform: scale(1.1); opacity: 1; }
  100% { transform: scale(0.95); opacity: 0.8; }
}

/* Estilos para notificaciones LEÍDAS */
.notification-item.read {
  background: #fafafa;
  border-left: 4px solid #ecf0f1;
  opacity: 0.8;
}

.notification-item.read:hover {
  background: #f5f5f5;
  opacity: 1;
}

.read-indicator {
  position: absolute;
  top: 15px;
  right: 15px;
  color: #27ae60;
}

.icon-small {
  width: 16px;
  height: 16px;
}

/* Estilos base comunes */
.notification-item {
  display: flex;
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  width: 100%;
  box-sizing: border-box;
  position: relative;
  cursor: pointer;
  transition: all 0.3s ease;
}

.notification-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 15px;
  font-size: 1.2rem;
  flex-shrink: 0;
}

/* Estilos por categoría */
.notification-icon.pago {
  color: #27ae60;
  background-color: #e8f8f0;
}

.notification-icon.envio {
  color: #3498db;
  background-color: #e1f0fa;
}

.notification-icon.horario {
  color: #f39c12;
  background-color: #fef5e7;
}

.notification-icon.sistema {
  color: #9b59b6;
  background-color: #f4ecf7;
}

.notification-icon.default {
  color: #95a5a6;
  background-color: #f8f9fa;
}

.icon {
  width: 20px;
  height: 20px;
}

.notification-details {
  flex: 1;
  min-width: 0;
}

.notification-details h3 {
  margin: 0 0 5px 0;
  font-size: 1rem;
  color: var(--text-dark);
  word-wrap: break-word;
}

.notification-details p {
  margin: 0 0 5px 0;
  font-size: 0.9rem;
  color: #666;
  word-wrap: break-word;
}

.notification-time {
  font-size: 0.8rem;
  color: #999;
  display: block;
}

.notification-category {
  font-size: 0.7rem;
  color: #bbb;
  text-transform: uppercase;
  display: block;
  margin-top: 2px;
}

.loading-state, .empty-state {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 40px;
  color: #666;
  text-align: center;
}

@media (max-width: 768px) {
  .notifications-header {
    padding: 12px 15px;
  }

  .filters-section {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
  }

  .filter-group {
    min-width: auto;
  }

  .custom-dates {
    flex-direction: column;
    gap: 10px;
  }

  .notification-item {
    padding: 12px;
  }
  
  .notification-item.unread {
    border-left-width: 3px;
  }
  
  .notification-item.read {
    border-left-width: 3px;
  }
}
</style>