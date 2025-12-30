<script setup>
import { ref, computed } from 'vue';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable'; // Importación corregida

// Props que recibirá el componente
const props = defineProps({
  salesData: {
    type: Array,
    required: true,
    default: () => []
  },
  summary: {
    type: Object,
    required: true,
    default: () => ({})
  },
  filters: {
    type: Object,
    required: true,
    default: () => ({})
  },
  userInfo: {
    type: Object,
    required: true,
    default: () => ({})
  }
});

// Método para generar el PDF del corte de ventas
const generateCutPDF = () => {
  // Crear documento PDF
  const doc = new jsPDF();
  
  // Colores corporativos
  const primaryColor = [230, 126, 34]; // Naranja RUTVANS
  const darkColor = [44, 62, 80]; // Azul oscuro
  const successColor = [40, 167, 69]; // Verde
  const lightGray = [248, 249, 250]; // Gris claro

  // Configuración inicial
  let yPosition = 15;
  const pageWidth = doc.internal.pageSize.width;
  const margin = 15;

  // ===== ENCABEZADO =====
  doc.setFillColor(...primaryColor);
  doc.rect(0, 0, pageWidth, 40, 'F');
  
  // Logo y título
  doc.setTextColor(255, 255, 255);
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(20);
  doc.text('RUTVANS', pageWidth / 2, 15, { align: 'center' });
  
  doc.setFontSize(14);
  doc.text('CORTE DE VENTAS', pageWidth / 2, 25, { align: 'center' });
  
  doc.setFontSize(10);
  doc.text('Sistema de Gestión de Transporte', pageWidth / 2, 32, { align: 'center' });

  yPosition = 50;

  // ===== INFORMACIÓN DEL CORTE =====
  doc.setFillColor(...lightGray);
  doc.rect(margin, yPosition, pageWidth - 2 * margin, 25, 'F');
  
  doc.setTextColor(...darkColor);
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(12);
  doc.text('INFORMACIÓN DEL CORTE', margin + 5, yPosition + 8);
  
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(9);
  doc.text(`Cajero: ${props.userInfo.name || 'N/A'}`, margin + 5, yPosition + 15);
  doc.text(`Fecha de corte: ${formatDate(props.filters.cutDate)}`, pageWidth / 2, yPosition + 15);
  doc.text(`Hora de generación: ${new Date().toLocaleTimeString('es-MX')}`, margin + 5, yPosition + 22);
  doc.text(`Estado: ${getStatusLabel(props.filters.status)}`, pageWidth / 2, yPosition + 22);

  yPosition += 35;

  // ===== ESTADÍSTICAS RESUMEN =====
  doc.setFillColor(233, 236, 239);
  doc.rect(margin, yPosition, pageWidth - 2 * margin, 20, 'F');
  
  doc.setTextColor(...darkColor);
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11);
  doc.text('RESUMEN DEL CORTE', margin + 5, yPosition + 8);
  
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(9);
  const statsY = yPosition + 15;
  doc.text(`Ventas totales: $${formatCurrency(props.summary.totalSales)}`, margin + 5, statsY);
  doc.text(`Boletos vendidos: ${props.summary.ticketsSold}`, pageWidth / 2, statsY);
  doc.text(`Ticket promedio: $${formatCurrency(props.summary.averageTicket)}`, margin + 5, statsY + 7);

  yPosition += 30;

  // ===== TABLA DE VENTAS =====
  if (props.salesData.length > 0) {
    // Preparar datos para la tabla
    const tableData = props.salesData.map(sale => [
      sale.folio,
      formatDateTime(sale.created_at),
      sale.passenger_name,
      `${sale.origin} → ${sale.destination}`,
      `Asiento ${sale.seat_number}`,
      sale.fare_type,
      `$${formatCurrency(sale.base_price)}`,
      sale.discount > 0 ? `-$${formatCurrency(sale.discount)}` : '$0.00',
      `$${formatCurrency(sale.amount)}`,
      getStatusLabel(sale.status)
    ]);

    // Usar autoTable como función independiente - FORMA CORRECTA
    autoTable(doc, {
      startY: yPosition,
      head: [[
        'Folio', 'Fecha/Hora', 'Pasajero', 'Ruta', 'Asiento', 
        'Tarifa', 'Precio', 'Desc.', 'Total', 'Estado'
      ]],
      body: tableData,
      theme: 'grid',
      headStyles: {
        fillColor: darkColor,
        textColor: 255,
        fontStyle: 'bold',
        fontSize: 8
      },
      bodyStyles: {
        fontSize: 7,
        cellPadding: 2
      },
      alternateRowStyles: {
        fillColor: [248, 249, 250]
      },
      margin: { left: margin, right: margin },
      styles: {
        overflow: 'linebreak',
        cellWidth: 'wrap'
      },
      columnStyles: {
        0: { cellWidth: 18 }, // Folio
        1: { cellWidth: 25 }, // Fecha/Hora
        2: { cellWidth: 25 }, // Pasajero
        3: { cellWidth: 30 }, // Ruta
        4: { cellWidth: 15 }, // Asiento
        5: { cellWidth: 15 }, // Tarifa
        6: { cellWidth: 15 }, // Precio
        7: { cellWidth: 12 }, // Desc.
        8: { cellWidth: 15 }, // Total
        9: { cellWidth: 15 }  // Estado
      }
    });

    // Obtener la posición Y final después de la tabla
    yPosition = doc.lastAutoTable.finalY + 10;
  } else {
    doc.setTextColor(100, 100, 100);
    doc.setFont('helvetica', 'italic');
    doc.setFontSize(10);
    doc.text('No hay ventas para mostrar en este corte', pageWidth / 2, yPosition + 10, { align: 'center' });
    yPosition += 20;
  }

  // ===== TOTALES FINALES =====
  if (props.salesData.length > 0) {
    doc.setFillColor(225, 239, 255);
    doc.rect(margin, yPosition, pageWidth - 2 * margin, 15, 'F');
    
    doc.setTextColor(...darkColor);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10);
    
    doc.text('TOTALES FINALES:', margin + 5, yPosition + 6);
    doc.text(`Base: $${formatCurrency(props.summary.totalBase)}`, margin + 60, yPosition + 6);
    doc.text(`Descuentos: -$${formatCurrency(props.summary.totalDiscount)}`, margin + 100, yPosition + 6);
    doc.text(`Neto: $${formatCurrency(props.summary.totalSales)}`, pageWidth - margin - 5, yPosition + 6, { align: 'right' });

    yPosition += 25;
  }

  // ===== PIE DE PÁGINA =====
  doc.setDrawColor(...lightGray);
  doc.line(margin, yPosition, pageWidth - margin, yPosition);
  
  yPosition += 10;
  
  doc.setTextColor(100, 100, 100);
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(8);
  doc.text('Documento generado automáticamente por el Sistema RUTVANS', pageWidth / 2, yPosition, { align: 'center' });
  doc.text(`Página 1 de 1`, pageWidth / 2, yPosition + 5, { align: 'center' });
  doc.text('www.rutvans.com', pageWidth / 2, yPosition + 10, { align: 'center' });

  // ===== GUARDAR PDF =====
  const fileName = `Corte_Ventas_${formatDate(props.filters.cutDate)}_${new Date().getTime()}.pdf`;
  doc.save(fileName);
};

// Funciones de formato (sin cambios)
const formatCurrency = (amount) => {
  const numericAmount = Number(amount) || 0;
  return new Intl.NumberFormat('es-MX', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(numericAmount);
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('es-MX');
};

const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('es-MX') + ' ' + date.toLocaleTimeString('es-MX', {
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getStatusLabel = (status) => {
  const labels = {
    'completed': 'Completada',
    'pending': 'Pendiente',
    'cancelled': 'Cancelada'
  };
  return labels[status] || status;
};

// Exponer el método para que pueda ser usado desde el componente padre
defineExpose({
  generateCutPDF
});
</script>

<template>
  <!-- Este componente no tiene template ya que es solo lógica para generar PDF -->
</template>