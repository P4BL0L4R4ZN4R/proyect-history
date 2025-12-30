<template>
  <div class="sales-cut">
    <div class="container-fluid">
      <!-- Header -->
      <div class="header-section">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h1 class="page-title">Corte de Ventas</h1>
            <p class="page-subtitle">Gestión y control de ventas diarias</p>
          </div>
          <div class="col-md-6 text-end">
            <div class="user-info-badge">
              <span class="user-name">{{ authStore.user?.name }}</span>
              <span class="user-role">Cajero</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Estadísticas Resumen -->
      <div class="row mb-4 stats-row">
        <div class="col-xl-3 col-md-6 h-100">
          <div class="stat-card total-sales">
            <div class="stat-icon">
              <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">${{ formatCurrency(summary.totalSales) }}</div>
              <div class="stat-label">Ventas Totales</div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 h-100">
          <div class="stat-card cash-sales">
            <div class="stat-icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ summary.ticketsSold }}</div>
              <div class="stat-label">Boletos Vendidos</div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 h-100">
          <div class="stat-card card-sales">
            <div class="stat-icon">
              <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">${{ formatCurrency(summary.averageTicket) }}</div>
              <div class="stat-label">Ticket Promedio</div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 h-100">
          <div class="stat-card tickets-sold">
            <div class="stat-icon">
              <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ formatDate(filters.cutDate) }}</div>
              <div class="stat-label">Fecha de Corte</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de Ventas -->
      <div class="sales-table-card">
        <div class="card-header">
          <!-- Filtros alineados de derecha a izquierda -->
          <div class="filters-section mb-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
              <h5 class="card-title mb-0">
                Detalle de Ventas 
                <span class="badge bg-primary ms-2">{{ sales.length }} ventas</span>
              </h5>
              
              <div class="table-filters d-flex align-items-end gap-3">
                <div class="filter-group">
                  <label class="form-label small">Fecha de Corte</label>
                  <input 
                    type="date" 
                    class="form-control form-control-sm minimal-input" 
                    v-model="filters.cutDate"
                    :max="today"
                  >
                </div>
                <div class="filter-group">
                  <label class="form-label small">Estado de Venta</label>
                  <select class="form-select form-select-sm minimal-select" v-model="filters.status">
                    <option value="completed">Completadas</option>
                    <option value="pending">Pendientes</option>
                    <option value="cancelled">Canceladas</option>
                    <option value="">Todos</option>
                  </select>
                </div>
                <div class="filter-group">
                  <button 
                    class="btn btn-primary btn-sm filter-btn apply-btn" 
                    @click="loadSalesData"
                    :disabled="loading"
                  >
                    <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                    {{ loading ? 'Cargando...' : 'Aplicar' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Acciones principales -->
          <div class="actions-section">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
              <div class="table-info">
                <small class="text-muted">
                  Mostrando {{ sales.length }} ventas del {{ formatDate(filters.cutDate) }}
                </small>
              </div>
              <div class="header-actions d-flex gap-2">
                <button 
                  class="btn btn-outline-primary btn-sm action-btn export-btn"
                  @click="exportToExcel"
                  :disabled="sales.length === 0"
                >
                  <i class="fas fa-file-excel me-2"></i> Exportar
                </button>
                <button 
                  class="btn btn-success btn-sm action-btn print-btn"
                  @click="generatePDFReport"
                  :disabled="sales.length === 0"
                >
                  <i class="fas fa-print me-2"></i> Imprimir Corte
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table minimal-table">
              <thead>
                <tr>
                  <th class="folio-col">Folio</th>
                  <th class="datetime-col">Fecha y Hora</th>
                  <th class="passenger-col">Pasajero</th>
                  <th class="route-col">Origen - Destino</th>
                  <th class="seat-col">Asiento</th>
                  <th class="fare-col">Tarifa</th>
                  <th class="price-col">Precio</th>
                  <th class="discount-col">Desc.</th>
                  <th class="total-col">Total</th>
                  <th class="status-col">Estado</th>
                  <th class="actions-col">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="11" class="text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status">
                      <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="text-muted mb-0">Cargando ventas...</p>
                  </td>
                </tr>
                <tr v-else-if="sales.length === 0">
                  <td colspan="11" class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                    <p class="mb-2">No se encontraron ventas para la fecha seleccionada</p>
                    <small class="text-muted">Fecha: {{ formatDate(filters.cutDate) }} | Estado: {{ getStatusLabel(filters.status) }}</small>
                  </td>
                </tr>
                <tr v-else v-for="sale in sales" :key="sale.id" class="table-row">
                  <td class="fw-bold text-primary folio-col">{{ sale.folio }}</td>
                  <td class="datetime-col">
                    <div class="small">{{ formatDate(sale.created_at) }}</div>
                    <div class="text-muted smaller">{{ formatTime(sale.created_at) }}</div>
                  </td>
                  <td class="passenger-col">{{ sale.passenger_name }}</td>
                  <td class="route-col">
                    <div class="small">{{ sale.origin }} → {{ sale.destination }}</div>
                    <div class="text-muted smaller" v-if="sale.travel_date">
                      {{ formatDate(sale.travel_date) }} {{ sale.departure_time }}
                    </div>
                  </td>
                  <td class="seat-col">
                    <span class="badge bg-light text-dark border">Asiento {{ sale.seat_number }}</span>
                  </td>
                  <td class="fare-col">
                    <span class="badge" :class="getFareTypeBadge(sale.fare_type)">
                      {{ sale.fare_type }}
                    </span>
                  </td>
                  <td class="price-col">${{ formatCurrency(sale.base_price) }}</td>
                  <td class="discount-col">
                    <span v-if="sale.discount > 0" class="text-success">
                      -${{ formatCurrency(sale.discount) }}
                    </span>
                    <span v-else class="text-muted">$0.00</span>
                  </td>
                  <td class="total-col fw-bold text-primary">${{ formatCurrency(sale.amount) }}</td>
                  <td class="status-col">
                    <span class="badge" :class="getStatusBadge(sale.status)">
                      {{ getStatusLabel(sale.status) }}
                    </span>
                  </td>
                  <td class="actions-col">
                    <div class="action-buttons d-flex gap-1">
                      <button 
                        class="btn btn-outline-primary btn-sm action-btn view-btn"
                        @click="viewSaleDetails(sale)"
                        title="Ver detalles"
                      >
                        <i class="fas fa-eye"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
              <tfoot v-if="sales.length > 0">
                <tr class="table-total-row">
                  <td colspan="6" class="text-end fw-bold">Totales:</td>
                  <td class="price-col fw-bold">${{ formatCurrency(summary.totalBase) }}</td>
                  <td class="discount-col fw-bold text-success">-${{ formatCurrency(summary.totalDiscount) }}</td>
                  <td class="total-col fw-bold">${{ formatCurrency(summary.totalSales) }}</td>
                  <td colspan="2"></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <!-- Modal de Detalles de Venta -->
      <div class="modal fade" id="saleDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Detalles de Venta - {{ selectedSale?.folio }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" v-if="selectedSale">
              <div class="row">
                <div class="col-md-6">
                  <h6>Información del Pasajero</h6>
                  <p><strong>Nombre:</strong> {{ selectedSale.passenger_name }}</p>
                  <p><strong>Asiento:</strong> {{ selectedSale.seat_number }}</p>
                  <p><strong>Tipo de Tarifa:</strong> 
                    <span class="badge" :class="getFareTypeBadge(selectedSale.fare_type)">
                      {{ selectedSale.fare_type }}
                    </span>
                  </p>
                </div>
                <div class="col-md-6">
                  <h6>Información del Viaje</h6>
                  <p><strong>Ruta:</strong> {{ selectedSale.origin }} → {{ selectedSale.destination }}</p>
                  <p><strong>Fecha del Viaje:</strong> {{ formatDate(selectedSale.travel_date) }}</p>
                  <p><strong>Hora de Salida:</strong> {{ selectedSale.departure_time }}</p>
                </div>
              </div>
              <div class="row mt-3">
                <div class="col-md-6">
                  <h6>Información de Pago</h6>
                  <p><strong>Método:</strong> 
                    <span class="badge bg-success">Efectivo</span>
                  </p>
                  <p><strong>Estado:</strong> 
                    <span class="badge" :class="getStatusBadge(selectedSale.status)">
                      {{ getStatusLabel(selectedSale.status) }}
                    </span>
                  </p>
                  <p><strong>Fecha de Venta:</strong> {{ formatDateTime(selectedSale.created_at) }}</p>
                </div>
                <div class="col-md-6">
                  <h6>Detalles Financieros</h6>
                  <p><strong>Precio Base:</strong> ${{ formatCurrency(selectedSale.base_price) }}</p>
                  <p><strong>Descuento:</strong> ${{ formatCurrency(selectedSale.discount) }}</p>
                  <p><strong>Total Pagado:</strong> <span class="fw-bold text-primary">${{ formatCurrency(selectedSale.amount) }}</span></p>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary minimal-btn" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Componente para generación de PDF (oculto) -->
      <FromSaleCut 
        ref="fromSaleCutRef"
        :sales-data="sales"
        :summary="summary"
        :filters="filters"
        :user-info="currentUser"
      />
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth'
import api from '@/api'
import { ref } from 'vue'
import FromSaleCut from '@/components/FromSaleCut.vue'

export default {
  name: 'SalesCut',
  components: {
    FromSaleCut
  },
  setup() {
    const authStore = useAuthStore()
    const fromSaleCutRef = ref(null)
    
    return { 
      authStore,
      fromSaleCutRef
    }
  },
  data() {
    return {
      loading: false,
      sales: [],
      summary: {
        totalSales: 0,
        totalBase: 0,
        totalDiscount: 0,
        ticketsSold: 0,
        averageTicket: 0
      },
      filters: {
        cutDate: new Date().toISOString().split('T')[0],
        status: 'completed'
      },
      selectedSale: null,
      today: new Date().toISOString().split('T')[0]
    }
  },
  computed: {
    currentUser() {
      return this.authStore.user
    }
  },
  mounted() {
    if (!this.authStore.hasRole('cashier')) {
      this.$router.push('/')
      return
    }
    
    this.loadSalesData()
  },
  methods: {
    async loadSalesData() {
      this.loading = true
      try {
        const response = await api.get('/sales/cashier-sales', {
          params: {
            date: this.filters.cutDate,
            status: this.filters.status,
            user_id: this.currentUser.id
          }
        })
        
        if (response.data.success) {
          this.sales = response.data.sales
          this.calculateSummary()
        } else {
          console.error('Error en la respuesta:', response.data.message)
          this.sales = []
        }
        
      } catch (error) {
        console.error('Error loading sales data:', error)
        this.sales = []
        if (error.response?.status === 404) {
          this.loadSampleData()
        }
      } finally {
        this.loading = false
      }
    },

    loadSampleData() {
      const sampleDate = this.filters.cutDate
      this.sales = [
        {
          id: 1,
          folio: 'F7A3B2C9',
          created_at: `${sampleDate}T08:30:00`,
          passenger_name: 'Juan Pérez',
          origin: 'Ciudad de México',
          destination: 'Puebla',
          seat_number: '05',
          fare_type: 'Adulto',
          amount: 250.00,
          base_price: 250.00,
          discount: 0,
          payment_method: 'cash',
          status: 'completed',
          travel_date: sampleDate,
          departure_time: '08:00'
        },
        {
          id: 2,
          folio: 'F8B4C3D1',
          created_at: `${sampleDate}T10:15:00`,
          passenger_name: 'María García',
          origin: 'Puebla',
          destination: 'Oaxaca',
          seat_number: '12',
          fare_type: 'Estudiante',
          amount: 180.00,
          base_price: 200.00,
          discount: 20.00,
          payment_method: 'cash',
          status: 'completed',
          travel_date: sampleDate,
          departure_time: '10:30'
        }
      ]
      this.calculateSummary()
    },

    calculateSummary() {
      const totalSales = this.sales.reduce((sum, sale) => {
        const amount = parseFloat(sale.amount) || 0;
        return sum + amount;
      }, 0);

      const totalBase = this.sales.reduce((sum, sale) => {
        const basePrice = parseFloat(sale.base_price) || 0;
        return sum + basePrice;
      }, 0);

      const totalDiscount = this.sales.reduce((sum, sale) => {
        const discount = parseFloat(sale.discount) || 0;
        return sum + discount;
      }, 0);

      this.summary = {
        totalSales: totalSales,
        totalBase: totalBase,
        totalDiscount: totalDiscount,
        ticketsSold: this.sales.length,
        averageTicket: this.sales.length > 0 ? totalSales / this.sales.length : 0
      }
    },

    viewSaleDetails(sale) {
      this.selectedSale = sale
      const modalElement = document.getElementById('saleDetailsModal')
      if (modalElement) {
        const modal = new bootstrap.Modal(modalElement)
        modal.show()
      }
    },

    async generatePDFReport() {
      if (this.sales.length === 0) {
        alert('No hay ventas para generar el reporte')
        return
      }

      try {
        // Esperar a que el componente se monte si es necesario
        await this.$nextTick()
        
        // Generar el PDF usando el componente
        if (this.fromSaleCutRef && this.fromSaleCutRef.generateCutPDF) {
          this.fromSaleCutRef.generateCutPDF()
        } else {
          console.error('Componente de PDF no disponible')
          alert('Error al generar el PDF. Por favor, intente nuevamente.')
        }
      } catch (error) {
        console.error('Error generando PDF:', error)
        alert('Error al generar el PDF. Por favor, intente nuevamente.')
      }
    },

    formatCurrency(amount) {
      return new Intl.NumberFormat('es-MX', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(amount)
    },

    formatDate(dateString) {
      if (!dateString) return 'N/A'
      return new Date(dateString).toLocaleDateString('es-MX')
    },

    formatTime(dateString) {
      if (!dateString) return 'N/A'
      return new Date(dateString).toLocaleTimeString('es-MX', {
        hour: '2-digit',
        minute: '2-digit'
      })
    },

    formatDateTime(dateString) {
      if (!dateString) return 'N/A'
      return new Date(dateString).toLocaleString('es-MX')
    },

    getFareTypeBadge(type) {
      const types = {
        'Adulto': 'bg-primary',
        'Estudiante': 'bg-success',
        'Niño': 'bg-info',
        'Tercera Edad': 'bg-warning',
        'General': 'bg-secondary'
      }
      return types[type] || 'bg-secondary'
    },

    getStatusBadge(status) {
      const statuses = {
        'completed': 'bg-success',
        'pending': 'bg-warning',
        'cancelled': 'bg-danger'
      }
      return statuses[status] || 'bg-secondary'
    },

    getStatusLabel(status) {
      const labels = {
        'completed': 'Completada',
        'pending': 'Pendiente',
        'cancelled': 'Cancelada'
      }
      return labels[status] || status
    },

    exportToExcel() {
      const data = {
        fecha: this.formatDate(this.filters.cutDate),
        ventas: this.sales,
        resumen: this.summary
      }
      console.log('Exporting data:', data)
      alert(`Funcionalidad de exportación en desarrollo\nVentas a exportar: ${this.sales.length}`)
    }
  },
  watch: {
    'filters.cutDate'() {
      this.loadSalesData()
    },
    'filters.status'() {
      this.loadSalesData()
    }
  }
}
</script>

<style scoped>
.sales-cut {
  padding: 20px;
  min-height: 100vh;
  background: #f8f9fa;
}

.header-section {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  margin-bottom: 1.5rem;
  border: 1px solid #e9ecef;
}

.page-title {
  color: #2c3e50;
  font-weight: 600;
  margin-bottom: 0.25rem;
  font-size: 1.5rem;
}

.page-subtitle {
  color: #6c757d;
  margin-bottom: 0;
  font-size: 0.9rem;
}

.user-info-badge {
  display: inline-flex;
  flex-direction: column;
  align-items: flex-end;
}

.user-name {
  font-weight: 500;
  color: #2c3e50;
  font-size: 0.95rem;
}

.user-role {
  font-size: 0.8rem;
  color: #6c757d;
}

.stats-row {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.stat-card {
  background: white;
  padding: 1.25rem;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
  height: 100%;
  border: 1px solid #e9ecef;
  transition: all 0.2s ease;
}

.stat-card:hover {
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  transform: translateY(-1px);
}

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  color: white;
  flex-shrink: 0;
}

.total-sales .stat-icon { background: #007bff; }
.cash-sales .stat-icon { background: #28a745; }
.card-sales .stat-icon { background: #6f42c1; }
.tickets-sold .stat-icon { background: #fd7e14; }

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 0.25rem;
}

.stat-label {
  color: #6c757d;
  font-size: 0.8rem;
}

.sales-table-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  overflow: hidden;
  margin-bottom: 1rem;
  border: 1px solid #e9ecef;
}

.card-header {
  background: #f8f9fa;
  padding: 1.5rem;
  border-bottom: 1px solid #e9ecef;
}

/* Sección de filtros mejorada */
.filters-section {
  padding-bottom: 1rem;
  border-bottom: 1px solid #e9ecef;
}

.table-filters {
  display: flex;
  align-items: end;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  flex-direction: column;
  min-width: 140px;
}

.filter-group .form-label {
  font-weight: 500;
  color: #495057;
  margin-bottom: 0.5rem;
  font-size: 0.8rem;
}

.minimal-input,
.minimal-select {
  font-size: 0.85rem;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  border: 1px solid #ced4da;
  background: white;
  transition: all 0.2s ease;
}

.minimal-input:focus,
.minimal-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
  outline: none;
}

.apply-btn {
  font-size: 0.85rem;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  background: #007bff;
  border: 1px solid #007bff;
  color: white;
  transition: all 0.2s ease;
  min-width: 100px;
  height: fit-content;
}

.apply-btn:hover:not(:disabled) {
  background: #0056b3;
  border-color: #0056b3;
  transform: translateY(-1px);
}

.apply-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* Sección de acciones mejorada */
.actions-section {
  padding-top: 1rem;
}

.header-actions {
  display: flex;
  gap: 0.75rem;
}

.export-btn,
.print-btn {
  font-size: 0.85rem;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  border: 1px solid #ced4da;
  background: white;
  color: #495057;
  transition: all 0.2s ease;
  min-width: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.export-btn:hover:not(:disabled) {
  background: #f8f9fa;
  border-color: #adb5bd;
  transform: translateY(-1px);
}

.print-btn:hover:not(:disabled) {
  background: #218838;
  border-color: #1e7e34;
  transform: translateY(-1px);
}

.export-btn {
  border-color: #007bff;
  color: #007bff;
}

.print-btn {
  background: #28a745;
  border-color: #28a745;
  color: white;
}

.card-title {
  margin: 0;
  color: #2c3e50;
  font-size: 1.1rem;
  font-weight: 500;
}

/* Tabla minimalista */
.minimal-table {
  margin-bottom: 0;
  border-collapse: separate;
  border-spacing: 0;
}

.minimal-table th {
  background: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  font-weight: 500;
  color: #495057;
  white-space: nowrap;
  padding: 1rem 0.75rem;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.minimal-table td {
  padding: 1rem 0.75rem;
  border-bottom: 1px solid #e9ecef;
  vertical-align: middle;
  font-size: 0.85rem;
}

.table-row:hover {
  background-color: #f8f9fa;
}

.table-total-row {
  background: #e3f2fd;
  font-weight: 500;
  border-top: 2px solid #007bff;
}

.table-total-row td {
  vertical-align: middle;
  padding: 1.25rem 0.75rem;
}

.badge {
  font-size: 0.7rem;
  padding: 0.35rem 0.65rem;
  border-radius: 4px;
  font-weight: 500;
}

.smaller {
  font-size: 0.75rem;
}

/* Botones de acción en la tabla */
.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.view-btn {
  padding: 0.4rem 0.6rem;
  border-radius: 4px;
  border: 1px solid #ced4da;
  background: white;
  font-size: 0.75rem;
  transition: all 0.2s ease;
  min-width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.view-btn:hover {
  background: #007bff;
  border-color: #007bff;
  color: white;
  transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
  .sales-cut {
    padding: 10px;
  }
  
  .header-section {
    padding: 1rem;
  }
  
  .stat-card {
    padding: 1rem;
    margin-bottom: 0;
  }
  
  .stat-value {
    font-size: 1.1rem;
  }
  
  .table-filters {
    flex-direction: column;
    width: 100%;
    gap: 1rem;
  }
  
  .filter-group {
    width: 100%;
  }
  
  .header-actions {
    flex-direction: column;
    width: 100%;
    gap: 0.5rem;
  }
  
  .header-actions .btn {
    width: 100%;
  }

  .minimal-table th,
  .minimal-table td {
    padding: 0.75rem 0.5rem;
    font-size: 0.8rem;
  }

  .action-buttons {
    flex-direction: column;
    gap: 0.25rem;
  }

  .view-btn {
    min-width: 32px;
    height: 32px;
  }
}

@media (max-width: 576px) {
  .minimal-table th:nth-child(n+4),
  .minimal-table td:nth-child(n+4):not(:nth-child(11)):not(:nth-child(10)) {
    display: none;
  }
  
  .card-header {
    padding: 1rem;
  }

  .table-filters {
    gap: 0.75rem;
  }
}
</style>