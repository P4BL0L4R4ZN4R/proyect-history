<template>
  <div class="form-ticket-container">
    <div class="content-container">
    <div class="ticket-form-section">
      <div class="passenger-card">
        <div class="passenger-info">
          <h3 class="passenger-title">Pasajero</h3>
          <p class="passenger-type">{{ passengerType }} (${{ total }} MXN)</p>
          <p v-if="seatNumber" class="passenger-seat">Asiento: {{ seatNumber }}</p>
          <div class="passenger-image">
            <img src="https://cdn-icons-png.flaticon.com/512/75/75648.png" alt="Pasajero" />
          </div>
        </div>
        <div class="separator-vertical"></div>
        <div class="passenger-form">
          <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" v-model="nombre" placeholder="Ingrese su nombre" required>
          </div>
          <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" v-model="apellido" placeholder="Ingrese su apellido" required>
          </div>
        </div>
      </div>
    </div>
    <ProgressBar :currentStep="currentStep" :progress="formProgress"/>
     </div>
    <div class="cart-section">
      <div class="cart">
        <div class="cart-header">
          <h2>Resumen de viaje</h2>
          <div class="header-content">
            <img :src="imagen" alt="viaje" class="header-image" />
            <div class="header-info">
              <p>{{ fechaF }} - {{ horaF }}</p>
              <p><strong>Origen:</strong> {{ origen }}</p>
              <p><strong>Destino:</strong> {{ destino }}</p>
              <p v-if="seatNumber"><strong>Asiento:</strong> {{ seatNumber }}</p>
            </div>
          </div>
        </div>
        <div class="header-separator"></div>
        <div class="cart-body">
          <div class="cart-item">
            <p>Costo de viaje:</p>
            <p>${{ precio_base }} MXN</p>
          </div>
          <div class="cart-item" v-if="discount > 0">
            <p>Descuento ({{ passengerType }}):</p>
            <p>-${{ discount }} MXN</p>
          </div>
          <div class="body-separator"></div>
          <div class="cart-item total">
            <p>Total:</p>
            <p>${{ total }} MXN</p>
          </div>
        </div>
        <div class="cart-footer">
          <button @click="imprimirTicket" class="print-btn">Imprimir Ticket</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
  import { ref, computed, onMounted } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import ProgressBar from '@/components/ProgressBar.vue';
  import api from '@/api';
  import jsPDF from 'jspdf';
  import JsBarcode from 'jsbarcode';

  const route = useRoute();
  const router = useRouter();

  const nombre = ref('');
  const apellido = ref('');

const formProgress = computed(() => {
  let progress = 75;
  if (nombre.value.trim().length > 0 || apellido.value.trim().length > 0) progress += 24;
  return progress;
})

const currentStep = computed(() => {
  let step = 3;
  if (nombre.value.trim().length > 0 || apellido.value.trim().length > 0) step += 1;
  if (nombre.value.trim().length > 0 && apellido.value.trim().length > 0) step += 1;
  return step;
})

  const passengerType = ref(route.query.type || 'Adulto');
  const precio_base = ref(Number(route.query.base_price) || 0);
  const discount = ref(Number(route.query.discount) || 0);
  const total = ref(Number(route.query.total_price) || 0);
  const seatNumber = ref(route.query.seatNumber || '');
  const origen = ref(route.query.origen || '');
  const destino = ref(route.query.destino || '');
  const fecha = ref(route.query.fecha || '');
  const hora = ref(route.query.hora || '');
  const fechaF = ref(route.query.fechaF || '');
  const horaF = ref(route.query.horaF || '');
  const imagen = ref(route.query.imagen || '');
  const companyName = ref(route.query.companyName || 'RUTVANS'); // Nuevo campo, con fallback a 'RUTVANS'

  const generarCodigoBarras = async (folio) => {
    const canvas = document.createElement('canvas');
    JsBarcode(canvas, folio, {
      format: 'CODE128',
      width: 2,
      height: 30,
      displayValue: false
    });
    return canvas.toDataURL('image/png');
  };

  const imprimirTicket = async () => {
    if (!nombre.value || !apellido.value) {
      alert('Por favor complete todos los campos');
      return;
    }

    const ticketData = {
      passenger_name: `${nombre.value} ${apellido.value}`,
      fare_type: passengerType.value,
      seat_number: parseInt(seatNumber.value),
      origin: origen.value,
      destination: destino.value,
      date: fecha.value,
      departure_time: hora.value,
      base_price: parseFloat(precio_base.value),
      discount: parseFloat(discount.value),
      total: parseFloat(total.value),
      route_unit_schedule_id: route.query.routeUnitScheduleId
    };

    try {
      const dbResponse = await api.post('/trips/sales', ticketData);
      const folio = dbResponse.data.folio;

      // Configuración del documento (80mm de ancho x 150mm de alto - tamaño estándar ticket)
      const doc = new jsPDF({
        orientation: 'p',
        unit: 'mm',
        format: [80, 150]
      });

      // Colores corporativos
      const primaryColor = [243, 156, 18]; // Naranja
      const darkColor = [44, 62, 80]; // Azul oscuro
      const lightColor = [200, 200, 200]; // Gris claro

      // Logo y encabezado
      doc.setFont('helvetica', 'bold');
      doc.setTextColor(...darkColor);
      doc.setFontSize(14);
      doc.text(companyName.value, 40, 10, { align: 'center' }); // Usar companyName en lugar de 'RUTVANS'
      
      // Línea decorativa
      doc.setDrawColor(...primaryColor);
      doc.setLineWidth(0.5);
      doc.line(15, 15, 65, 15);

      // Información básica
      doc.setFontSize(7);
      doc.setTextColor(100, 100, 100);
      doc.text(`Folio: ${folio}`, 5, 20);
      doc.text(`Fecha: ${new Date().toLocaleDateString()}`, 5, 24);
      doc.text(`Hora: ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`, 40, 24);

      // Sección de viaje
      doc.setFontSize(9);
      doc.setTextColor(...darkColor);
      doc.setFont('helvetica', 'bold');
      doc.text('INFORMACIÓN DEL VIAJE', 40, 30, { align: 'center' });
      
      doc.setFont('helvetica', 'bold');
      doc.text(`${origen.value} ➔ ${destino.value}`, 40, 35, { align: 'center' });
      
      // Tabla de información
      doc.setFontSize(8);
      doc.text(`Salida:`, 5, 40);
      doc.text(`${fecha.value} ${hora.value}`, 25, 40);
      
      doc.text(`Asiento:`, 5, 45);
      doc.text(seatNumber.value || 'No asignado', 25, 45);
      
      doc.text(`Tipo:`, 5, 50);
      doc.text(passengerType.value, 25, 50);

      // Línea divisoria
      doc.setDrawColor(...lightColor);
      doc.line(5, 55, 75, 55);
      
      // Información del pasajero
      doc.setFont('helvetica', 'bold');
      doc.text('PASAJERO', 40, 60, { align: 'center' });
      
      doc.setFont('helvetica', 'normal');
      doc.text(`${nombre.value} ${apellido.value}`, 40, 65, { align: 'center' });

      // Línea divisoria
      doc.line(5, 70, 75, 70);

      // Detalles de pago
      doc.setFont('helvetica', 'bold');
      doc.text('DETALLE DE PAGO', 40, 75, { align: 'center' });
      
      doc.setFont('helvetica', 'normal');
      doc.text(`Tarifa base:`, 5, 80);
      doc.text(`$${precio_base.value.toFixed(2)}`, 60, 80, { align: 'right' });
      
      if (discount.value > 0) {
        doc.text(`Descuento (${passengerType.value}):`, 5, 85);
        doc.text(`-$${discount.value.toFixed(2)}`, 60, 85, { align: 'right' });
      }
      
      // Total
      doc.setFont('helvetica', 'bold');
      doc.text(`TOTAL:`, 5, 92);
      doc.text(`$${total.value.toFixed(2)} MXN`, 60, 92, { align: 'right' });

      // Código de barras
      const codigoBarrasImg = await generarCodigoBarras(folio);
      doc.addImage(codigoBarrasImg, 'PNG', 15, 100, 50, 15);
      
      doc.setFontSize(6);
      doc.text(folio, 40, 115, { align: 'center' });

      // Pie de página
      doc.setFontSize(7);
      doc.setTextColor(100, 100, 100);
      doc.text('Presente este ticket al abordar', 40, 125, { align: 'center' });
      doc.text('Válido solo para fecha y hora indicadas', 40, 130, { align: 'center' });
      doc.text('www.rutvans.com', 40, 140, { align: 'center' });

      // Generar y abrir PDF
      const pdfBlob = doc.output('blob');
      const pdfURL = URL.createObjectURL(pdfBlob);
      window.open(pdfURL, '_blank');

      alert(`Ticket ${folio} generado para ${nombre.value} ${apellido.value}`);
      
      // Redirigir a VentaAsientos con el mismo routeUnitScheduleId
      router.push({
        name: 'VentaAsientos',
        query: {
          routeUnitScheduleId: route.query.routeUnitScheduleId,
          origen: origen.value,
          destino: destino.value,
          fecha: fecha.value,
          hora_salida: hora.value,
          hora_llegada: horaF.value,
          precio_base: precio_base.value,
          imagen: imagen.value,
          companyName: companyName.value // Nuevo parámetro
        }
      });
    } catch (error) {
      console.error('Error al crear ticket:', error.response?.data);
      alert(`Error: ${error.response?.data?.message || 'Error al generar el ticket. Por favor intente nuevamente.'}`);
    }
  };
</script>

<style scoped>
  .form-ticket-container {
    display: flex;
    gap: 40px; /* Espacio horizontal entre tarjetas */
    padding: 30px;
    background-color: white;
    min-height: 100vh;
    width: 100%;
  }
  .content-container {
    flex-direction: column;
    width: 70%;
  }
  .ticket-form-section {
    flex: 2;
  }
  .cart-section {
    flex: 1;
  }
  .passenger-card {
    display: flex;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    gap: 20px;
  }
  .passenger-info {
    display: flex;
    flex-direction: column;
    min-width: 100px;
  }
  .passenger-title {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 5px;
    color: #2c3e50;
  }
  .passenger-type {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 15px;
  }
  .passenger-image img {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    object-fit: cover;
  }
  .separator-vertical {
    width: 1px;
    background-color: #ddd;
  }
  .passenger-form {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 15px;
  }
  .form-group {
    display: flex;
    flex-direction: column;
  }
  .form-group label {
    font-size: 0.9rem;
    margin-bottom: 5px;
    color: #555;
  }
  .form-group input {
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 6px;
    outline-color: #f39c12;
    transition: border-color 0.3s;
  }
  .form-group input:focus {
    border-color: #f39c12;
    box-shadow: 0 0 5px rgba(243, 156, 18, 0.4);
  }
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
  .print-btn {
    width: 100%;
    padding: 10px 20px;
    background-color: #f39c12;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s;
    justify-content: center;
  }
  .print-btn:hover {
    background-color: #e67e22;
  }
</style>
