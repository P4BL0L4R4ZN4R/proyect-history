<template>
  <div class="envios-container">
    <!-- Header Section -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-text">
          <h1 class="page-title">Gestión de Envíos</h1>
          <p class="page-subtitle">Administra y monitorea todos tus envíos en un solo lugar</p>
        </div>
        <div class="header-stats">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="bi bi-box"></i>
            </div>
            <div class="stat-info">
              <span class="stat-number">{{ envios.length }}</span>
              <span class="stat-label">Total Envíos</span>
            </div>
          </div>
          <div class="stat-card" v-if="envios.length > 0">
            <div class="stat-icon" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
              <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="stat-info">
              <span class="stat-number">{{ formatCurrency(totalAmount) }}</span>
              <span class="stat-label">Total Valor</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="loading-spinner"></div>
        <p>Cargando envíos...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="error-state">
        <div class="error-icon">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
        <h3>Error al cargar los datos</h3>
        <p>{{ error }}</p>
        <button class="retry-btn" @click="loadEnvios">
          <i class="bi bi-arrow-clockwise"></i>
          Reintentar
        </button>
      </div>

      <!-- Envíos Table -->
      <div v-else class="envios-table-container">
        <div class="table-header">
          <h2>Lista de Envíos</h2>
          <div class="table-actions">
            <!-- <button class="btn-primary" @click="navigateToCreate">
              <i class="bi bi-plus-circle"></i>
              Nuevo Envío
            </button> -->
            <button class="btn-secondary" @click="exportToCSV">
              <i class="bi bi-download"></i>
              Exportar
            </button>
          </div>
        </div>

        <div class="table-wrapper">
          <table class="envios-table">
            <thead>
              <tr>
                <th class="text-center">ID</th>
                <th>Folio</th>
                <th>Emisor</th>
                <th>Receptor</th>
                <th class="text-right">Monto</th>
                <th>Descripción</th>
                <th class="text-center">Imagen</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Dimensiones</th>
                <th class="text-center">Peso</th>
                <!-- <th class="text-center">Acciones</th> -->
              </tr>
            </thead>
            <tbody>
              <tr v-for="envio in envios" :key="envio.id" class="table-row">
                <td class="text-center envio-id">{{ envio.id }}</td>
                <td class="envio-folio">
                  <span class="folio-badge">{{ envio.folio }}</span>
                </td>
                <td class="envio-sender">
                  <div class="contact-info">
                    <i class="bi bi-person"></i>
                    {{ envio.sender_name || 'N/A' }}
                  </div>
                </td>
                <td class="envio-receiver">
                  <div class="contact-info">
                    <i class="bi bi-person-badge"></i>
                    {{ envio.receiver_name }}
                  </div>
                  <small class="text-muted">{{ truncateText(envio.receiver_description, 20) }}</small>
                </td>
                <td class="text-right envio-amount">
                  <span class="amount-badge">{{ formatCurrency(envio.amount) }}</span>
                </td>
                <td class="envio-description">
                  <span class="description-text" :title="envio.package_description">
                    {{ truncateText(envio.package_description, 40) }}
                  </span>
                </td>
                <td class="text-center envio-image">
                  <div v-if="envio.package_image_url" class="image-preview">
                    <img 
                      :src="envio.package_image_url" 
                      :alt="'Imagen de ' + envio.package_description"
                      class="package-image"
                      @click="viewImage(envio.package_image_url)"
                    />
                  </div>
                  <div v-else class="no-image">
                    <i class="bi bi-image"></i>
                    <span>Sin imagen</span>
                  </div>
                </td>
                <td class="text-center envio-status">
                  <span :class="['status-badge', getStatusBadgeClass(envio.status)]">
                    {{ getStatusText(envio.status) }}
                  </span>
                </td>
                <td class="text-center envio-dimensions">
                  <span class="dimensions-badge">
                    {{ envio.length_cm }}×{{ envio.width_cm }}×{{ envio.height_cm }} cm
                  </span>
                </td>
                <td class="text-center envio-weight">
                  <span class="weight-badge">
                    {{ envio.weight_kg }} kg
                    <i v-if="envio.fragile" class="bi bi-exclamation-triangle ms-1" title="Frágil"></i>
                  </span>
                </td>
                <!-- <td class="text-center envio-actions">
                  <button class="btn-action" title="Ver detalles" @click="viewDetails(envio.id)">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button class="btn-action" title="Editar" @click="editEnvio(envio.id)">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-action btn-danger" title="Eliminar" @click="deleteEnvio(envio.id)">
                    <i class="bi bi-trash"></i>
                  </button>
                </td> -->
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="envios.length === 0 && !loading" class="empty-state">
          <div class="empty-icon">
            <i class="bi bi-box"></i>
          </div>
          <h3>No hay envíos registrados</h3>
          <p>Comienza creando tu primer envío para verlo listado aquí.</p>
          <button class="btn-primary" @click="navigateToCreate">
            <i class="bi bi-plus-circle"></i>
            Crear Primer Envío
          </button>
        </div>
      </div>
    </div>

    <!-- Modal para ver imagen -->
    <div v-if="showImageModal" class="image-modal" @click.self="closeImageModal">
      <div class="modal-content">
        <button class="close-modal" @click="closeImageModal">
          <i class="bi bi-x-lg"></i>
        </button>
        <img :src="selectedImage" alt="Imagen del paquete" class="modal-image" />
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'EnviosList',
  
  setup() {
    // Estado reactivo
    const envios = ref([])
    const loading = ref(true)
    const error = ref(null)
    const showImageModal = ref(false)
    const selectedImage = ref('')

    // Computed properties
    const totalAmount = computed(() => {
      return envios.value.reduce((sum, envio) => sum + parseFloat(envio.amount || 0), 0)
    })

    // Métodos
    const loadEnvios = async () => {
      try {
        loading.value = true
        error.value = null
        const response = await axios.get('http://127.0.0.1:8000/api/shippings')
        envios.value = response.data
      } catch (err) {
        console.error('Error al cargar envíos:', err)
        error.value = 'Error al cargar los envíos. Por favor, intenta nuevamente.'
      } finally {
        loading.value = false
      }
    }

    const getStatusBadgeClass = (status) => {
      const statusClasses = {
        'Solicitado': 'status-pending',
        'Pagado': 'status-paid',
        'En camino': 'status-transit',
        'En terminal': 'status-terminal',
        'Cancelado': 'status-cancelled',
        'Expirado': 'status-expired'
      }
      return statusClasses[status] || 'status-default'
    }

    const getStatusText = (status) => {
      return status || 'Desconocido'
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
        minimumFractionDigits: 2
      }).format(amount || 0)
    }

    const truncateText = (text, maxLength = 30) => {
      if (!text) return 'Sin descripción'
      if (text.length <= maxLength) return text
      return text.substring(0, maxLength) + '...'
    }

    const viewImage = (imageUrl) => {
      selectedImage.value = imageUrl
      showImageModal.value = true
    }

    const closeImageModal = () => {
      showImageModal.value = false
      selectedImage.value = ''
    }

    const viewDetails = (id) => {
      console.log('Ver detalles del envío:', id)
      // Implementar lógica de navegación
    }

    const editEnvio = (id) => {
      console.log('Editar envío:', id)
      // Implementar lógica de navegación
    }

    const deleteEnvio = async (id) => {
      if (confirm('¿Estás seguro de que deseas eliminar este envío?')) {
        try {
          await axios.delete(`http://127.0.0.1:8000/api/shippings/${id}`)
          envios.value = envios.value.filter(envio => envio.id !== id)
          alert('Envío eliminado correctamente')
        } catch (error) {
          console.error('Error al eliminar:', error)
          alert('Error al eliminar el envío')
        }
      }
    }

    const navigateToCreate = () => {
      console.log('Navegar a crear envío')
      // Implementar lógica de navegación
    }

    const exportToCSV = () => {
      try {
        const headers = [
          'ID', 'Folio', 'Emisor', 'Receptor', 'Monto', 
          'Descripción', 'Estado', 'Dimensiones', 'Peso', 'Frágil'
        ]
        
        const csvContent = [
          headers.join(','),
          ...envios.value.map(envio => [
            envio.id,
            `"${envio.folio}"`,
            `"${envio.sender_name}"`,
            `"${envio.receiver_name}"`,
            envio.amount,
            `"${envio.package_description?.replace(/"/g, '""') || ''}"`,
            `"${envio.status}"`,
            `"${envio.length_cm}x${envio.width_cm}x${envio.height_cm} cm"`,
            `${envio.weight_kg} kg`,
            envio.fragile ? 'Sí' : 'No'
          ].join(','))
        ].join('\n')
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
        const link = document.createElement('a')
        link.href = URL.createObjectURL(blob)
        link.download = `envios_${new Date().toISOString().split('T')[0]}.csv`
        link.click()
        
        alert('CSV exportado correctamente')
      } catch (error) {
        console.error('Error al exportar CSV:', error)
        alert('Error al exportar el archivo CSV')
      }
    }

    // Ciclo de vida
    onMounted(() => {
      loadEnvios()
    })

    return {
      envios,
      loading,
      error,
      showImageModal,
      selectedImage,
      totalAmount,
      loadEnvios,
      getStatusBadgeClass,
      getStatusText,
      formatCurrency,
      truncateText,
      viewImage,
      closeImageModal,
      viewDetails,
      editEnvio,
      deleteEnvio,
      navigateToCreate,
      exportToCSV
    }
  }
}
</script>

<style scoped>
.envios-container {
  min-height: 100vh;
  background: #f8f9fa;
}

/* Header Styles */
.page-header {
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  color: white;
  padding: 40px 0;
  margin-bottom: 30px;
}

.header-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 30px;
}

.header-text {
  flex: 1;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 10px;
  color: white;
}

.page-subtitle {
  font-size: 1.1rem;
  color: #bdc3c7;
  margin: 0;
}

.header-stats {
  display: flex;
  gap: 20px;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 20px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  min-width: 180px;
}

.stat-icon {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #f39c12, #e67e22);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-number {
  font-size: 1.8rem;
  font-weight: 700;
  color: white;
}

.stat-label {
  font-size: 0.9rem;
  color: #bdc3c7;
}

/* Main Content */
.main-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px 40px;
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 60px 20px;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #e3e3e3;
  border-top: 4px solid #f39c12;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Error State */
.error-state {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.error-icon {
  font-size: 3rem;
  color: #e74c3c;
  margin-bottom: 20px;
}

.error-state h3 {
  color: #2c3e50;
  margin-bottom: 10px;
}

.error-state p {
  color: #7f8c8d;
  margin-bottom: 20px;
}

.retry-btn {
  background: #3498db;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  transition: background 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.retry-btn:hover {
  background: #2980b9;
}

/* Table Styles */
.envios-table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.table-header {
  padding: 25px 30px;
  border-bottom: 1px solid #eaeaea;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
}

.table-header h2 {
  color: #2c3e50;
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0;
}

.table-actions {
  display: flex;
  gap: 12px;
}

.btn-primary, .btn-secondary {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-primary {
  background: linear-gradient(135deg, #f39c12, #e67e22);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
}

.btn-secondary {
  background: #ecf0f1;
  color: #2c3e50;
  border: 1px solid #bdc3c7;
}

.btn-secondary:hover {
  background: #d5dbdb;
  transform: translateY(-1px);
}

.table-wrapper {
  overflow-x: auto;
  padding: 0 30px;
}

.envios-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 1200px;
}

.envios-table th {
  background: #f8f9fa;
  padding: 15px 12px;
  font-weight: 600;
  color: #2c3e50;
  border-bottom: 2px solid #eaeaea;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.envios-table td {
  padding: 15px 12px;
  border-bottom: 1px solid #eaeaea;
  color: #2c3e50;
}

.table-row:hover {
  background: #f8f9fa;
}

/* Column Specific Styles */
.envio-id {
  font-weight: 600;
  color: #f39c12;
}

.folio-badge {
  background: #ecf0f1;
  padding: 4px 8px;
  border-radius: 4px;
  font-family: 'Courier New', monospace;
  font-size: 0.85rem;
  color: #2c3e50;
}

.contact-info {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
}

.contact-info i {
  color: #7f8c8d;
  width: 16px;
}

.text-muted {
  color: #7f8c8d;
  font-size: 0.8rem;
  display: block;
  margin-top: 4px;
}

.amount-badge {
  background: linear-gradient(135deg, #27ae60, #2ecc71);
  color: white;
  padding: 6px 12px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.85rem;
}

.description-text {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  overflow: hidden;
  font-size: 0.9rem;
  line-height: 1.4;
  color: #5d6d7e;
}

/* Image Styles */
.image-preview {
  display: flex;
  justify-content: center;
  align-items: center;
}

.package-image {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 6px;
  cursor: pointer;
  transition: transform 0.3s ease;
  border: 2px solid #eaeaea;
}

.package-image:hover {
  transform: scale(1.1);
  border-color: #f39c12;
}

.no-image {
  display: flex;
  flex-direction: column;
  align-items: center;
  color: #bdc3c7;
  font-size: 0.8rem;
}

.no-image i {
  font-size: 1.5rem;
  margin-bottom: 4px;
}

/* Status Badges */
.status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: inline-block;
  min-width: 100px;
}

.status-pending {
  background: #fff3cd;
  color: #856404;
  border: 1px solid #ffeaa7;
}

.status-paid {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.status-transit {
  background: #cce7ff;
  color: #004085;
  border: 1px solid #b3d7ff;
}

.status-terminal {
  background: #d1ecf1;
  color: #0c5460;
  border: 1px solid #bee5eb;
}

.status-cancelled {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.status-expired {
  background: #f8f9fa;
  color: #6c757d;
  border: 1px solid #e9ecef;
}

.status-default {
  background: #f8f9fa;
  color: #6c757d;
  border: 1px solid #e9ecef;
}

/* Dimensions and Weight Badges */
.dimensions-badge, .weight-badge {
  background: #e8f4fd;
  color: #2980b9;
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 0.85rem;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.weight-badge i {
  color: #e74c3c;
  font-size: 0.8rem;
}

.ms-1 {
  margin-left: 4px;
}

/* Action Buttons */
.envio-actions {
  display: flex;
  gap: 8px;
  justify-content: center;
}

.btn-action {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  background: #ecf0f1;
  color: #2c3e50;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-action:hover {
  background: #3498db;
  color: white;
  transform: translateY(-2px);
}

.btn-danger:hover {
  background: #e74c3c;
  color: white;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  font-size: 4rem;
  color: #bdc3c7;
  margin-bottom: 20px;
}

.empty-state h3 {
  color: #2c3e50;
  margin-bottom: 10px;
}

.empty-state p {
  color: #7f8c8d;
  margin-bottom: 30px;
}

/* Image Modal */
.image-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  position: relative;
  max-width: 90%;
  max-height: 90%;
}

.close-modal {
  position: absolute;
  top: -40px;
  right: 0;
  background: none;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
}

.close-modal:hover {
  background: rgba(255, 255, 255, 0.2);
}

.modal-image {
  max-width: 100%;
  max-height: 80vh;
  object-fit: contain;
  border-radius: 8px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    text-align: center;
  }

  .page-title {
    font-size: 2rem;
  }

  .table-header {
    flex-direction: column;
    align-items: stretch;
  }

  .table-actions {
    justify-content: center;
  }

  .envios-table {
    font-size: 0.8rem;
  }

  .envios-table th,
  .envios-table td {
    padding: 10px 8px;
  }

  .stat-card {
    min-width: 150px;
  }
}

@media (max-width: 480px) {
  .main-content {
    padding: 0 15px 30px;
  }

  .table-header {
    padding: 20px;
  }

  .btn-primary, .btn-secondary {
    padding: 8px 16px;
    font-size: 0.8rem;
  }

  .stat-card {
    flex-direction: column;
    text-align: center;
    gap: 10px;
  }
}

.text-center {
  text-align: center;
}

.text-right {
  text-align: right;
}
</style>