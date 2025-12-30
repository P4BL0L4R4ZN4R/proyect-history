import 'package:flutter/material.dart';
import 'package:qr_flutter/qr_flutter.dart';
import 'package:rutvans_mobile/src/screens/home_page.dart';
import 'package:intl/intl.dart';
import 'package:pdf/widgets.dart' as pw;
import 'package:printing/printing.dart';
import 'package:pdf/pdf.dart';

/// Modelo Ticket
class Ticket {
  final String serviceType; // "pasaje", "envio", "flete"
  final String paymentMethod; // "efectivo", "tarjeta", "qr"
  final List<int> asientos;
  final Map<String, dynamic> detalleViaje;
  final String folio;
  final double total;
  final String origin;
  final String destination;
  final Duration duration;
  final String driverName;
  final DateTime travelDate;

  Ticket({
    required this.serviceType,
    required this.paymentMethod,
    required this.asientos,
    required this.detalleViaje,
    required this.folio,
    required this.total,
    required this.origin,
    required this.destination,
    required this.duration,
    required this.driverName,
    required this.travelDate,
  });
}

/// Página Ticket
class TicketPage extends StatelessWidget {
  final Ticket ticket;

  const TicketPage({super.key, required this.ticket});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text("Tu Ticket"),
        centerTitle: true,
        backgroundColor: Colors.orange,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Center(
          child: Card(
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(24),
            ),
            elevation: 6,
            shadowColor: Colors.black26,
            child: Padding(
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const Icon(
                    Icons.confirmation_number_rounded,
                    size: 80,
                    color: Colors.orange,
                  ),
                  const SizedBox(height: 16),
                  Center(
                    child: Text(
                      "Folio: ${ticket.folio}",
                      style: const TextStyle(
                        fontSize: 22,
                        fontWeight: FontWeight.bold,
                        color: Colors.black87,
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  Divider(color: Colors.grey.shade300, thickness: 1),

                  // Datos del viaje
                  _buildSectionTitle("Detalles del viaje"),
                  _buildDetailRow("Origen", ticket.origin),
                  _buildDetailRow("Destino", ticket.destination),
                  _buildDetailRow("Duración", _formatDuration(ticket.duration)),
                  _buildDetailRow("Conductor", ticket.driverName),
                  _buildDetailRow("Fecha", DateFormat('dd/MM/yyyy').format(ticket.travelDate)),
                  _buildDetailRow("Hora", DateFormat('h:mm a').format(ticket.travelDate)),
                  const SizedBox(height: 12),
                  Divider(color: Colors.grey.shade300, thickness: 1),

                  // Información de compra
                  _buildSectionTitle("Información de compra"),
                  _buildDetailRow("Asientos", ticket.asientos.join(", ")),
                  _buildDetailRow("Total", "\$${ticket.total.toStringAsFixed(2)}"),
                  const SizedBox(height: 12),

                  // Método de pago
                  _buildSectionTitle("Método de pago"),
                  _buildPaymentWidget(),
                  const SizedBox(height: 12),

                  // Botón imprimir PDF
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.blue,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16)),
                      ),
                      onPressed: _printTicket,
                      icon: const Icon(Icons.print),
                      label: const Text("Imprimir Ticket"),
                    ),
                  ),

                  const SizedBox(height: 12),

                  // Botón de volver al inicio
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.deepOrange,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(
                            vertical: 16, horizontal: 24),
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16)),
                        textStyle: const TextStyle(
                            fontSize: 16, fontWeight: FontWeight.bold),
                      ),
                      onPressed: () {
                        Navigator.pushAndRemoveUntil(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const HomePage(),
                          ),
                          (Route<dynamic> route) => false,
                        );
                      },
                      icon: const Icon(Icons.home),
                      label: const Text("Volver al inicio"),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  /// Sección de título
  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8, top: 12),
      child: Text(
        title,
        style: const TextStyle(
          fontSize: 18,
          fontWeight: FontWeight.bold,
          color: Colors.deepOrange,
        ),
      ),
    );
  }

  /// Fila de detalle
  Widget _buildDetailRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        children: [
          Expanded(
            flex: 4,
            child: Text(
              label,
              style: const TextStyle(
                fontWeight: FontWeight.w600,
                color: Colors.black87,
              ),
            ),
          ),
          Expanded(
            flex: 6,
            child: Text(
              value,
              textAlign: TextAlign.right,
              style: const TextStyle(
                fontWeight: FontWeight.w400,
                color: Colors.black54,
              ),
            ),
          ),
        ],
      ),
    );
  }

  /// Widget método de pago
  Widget _buildPaymentWidget() {
    switch (ticket.paymentMethod.toLowerCase()) {
      case "tarjeta":
        return Row(
          children: const [
            Icon(Icons.credit_card, color: Colors.blue, size: 32),
            SizedBox(width: 12),
            Text(
              "Tarjeta",
              style: TextStyle(
                fontWeight: FontWeight.w500,
                fontSize: 16,
                color: Colors.black87,
              ),
            ),
          ],
        );
      case "efectivo":
        return Row(
          children: const [
            Icon(Icons.attach_money, color: Colors.green, size: 32),
            SizedBox(width: 12),
            Text(
              "Efectivo",
              style: TextStyle(
                fontWeight: FontWeight.w500,
                fontSize: 16,
                color: Colors.black87,
              ),
            ),
          ],
        );
      case "qr":
        return Center(
          child: QrImageView(
            data: ticket.folio,
            version: QrVersions.auto,
            size: 180,
            backgroundColor: Colors.white,
          ),
        );
      default:
        return Text(
          "Método de pago: ${ticket.paymentMethod.toUpperCase()}",
          style: const TextStyle(fontWeight: FontWeight.w500),
        );
    }
  }

  /// Formato de duración
  String _formatDuration(Duration duration) {
    final hours = duration.inHours;
    final minutes = duration.inMinutes.remainder(60);
    return "${hours}h ${minutes}m";
  }

  /// Generar PDF e imprimir con estilo
Future<void> _printTicket() async {
  final pdf = pw.Document();

  pdf.addPage(
    pw.Page(
      pageFormat: PdfPageFormat.a4,
      margin: const pw.EdgeInsets.all(24),
      build: (pw.Context context) {
        return pw.Column(
          crossAxisAlignment: pw.CrossAxisAlignment.stretch,
          children: [
            // Encabezado
            pw.Container(
              padding: const pw.EdgeInsets.symmetric(vertical: 16),
              color: PdfColors.orange100,
              child: pw.Center(
                child: pw.Text(
                  'TICKET DE VIAJE',
                  style: pw.TextStyle(
                    fontSize: 30,
                    fontWeight: pw.FontWeight.bold,
                    color: PdfColors.orange800,
                  ),
                ),
              ),
            ),
            pw.SizedBox(height: 16),

            // Folio
           pw.Text(
  'Folio: ${ticket.folio}',
  style: pw.TextStyle(
    fontSize: 18,
    fontWeight: pw.FontWeight.bold, // ✅ sin const
  ),
),

            pw.SizedBox(height: 12),

            // Detalles del viaje
            pw.Container(
              padding: const pw.EdgeInsets.all(12),
              decoration: pw.BoxDecoration(
                color: PdfColors.grey200,
                borderRadius: const pw.BorderRadius.all(pw.Radius.circular(8)),
              ),
              child: pw.Column(
                crossAxisAlignment: pw.CrossAxisAlignment.start,
                children: [
                  pw.Text(
                    'Detalles del viaje',
                    style: pw.TextStyle(
                        fontSize: 16,
                        fontWeight: pw.FontWeight.bold,
                        color: PdfColors.blue800),
                  ),
                  pw.SizedBox(height: 6),
                  pw.Text('Origen: ${ticket.origin}',
                      style: const pw.TextStyle(fontSize: 14)),
                  pw.Text('Destino: ${ticket.destination}',
                      style: const pw.TextStyle(fontSize: 14)),
                  pw.Text('Duración: ${_formatDuration(ticket.duration)}',
                      style: const pw.TextStyle(fontSize: 14)),
                  pw.Text('Conductor: ${ticket.driverName}',
                      style: const pw.TextStyle(fontSize: 14)),
                  pw.Text(
                      'Fecha: ${DateFormat('dd/MM/yyyy').format(ticket.travelDate)}',
                      style: const pw.TextStyle(fontSize: 14)),
                  pw.Text(
                      'Hora: ${DateFormat('h:mm a').format(ticket.travelDate)}',
                      style: const pw.TextStyle(fontSize: 14)),
                ],
              ),
            ),
            pw.SizedBox(height: 16),

            // Información de compra
            pw.Container(
              padding: const pw.EdgeInsets.all(12),
              decoration: pw.BoxDecoration(
                color: PdfColors.green50,
                borderRadius: const pw.BorderRadius.all(pw.Radius.circular(8)),
              ),
              child: pw.Column(
                crossAxisAlignment: pw.CrossAxisAlignment.start,
                children: [
                  pw.Text('Información de compra',
                      style: pw.TextStyle(
                          fontSize: 16,
                          fontWeight: pw.FontWeight.bold,
                          color: PdfColors.green800)),
                  pw.SizedBox(height: 6),
                  pw.Text('Asientos: ${ticket.asientos.join(", ")}',
                      style: const pw.TextStyle(fontSize: 14)),
                  pw.Text('Total: \$${ticket.total.toStringAsFixed(2)}',
                      style: pw.TextStyle(
                          fontSize: 16,
                          fontWeight: pw.FontWeight.bold,
                          color: PdfColors.green900)),
                ],
              ),
            ),
            pw.SizedBox(height: 16),

            // Método de pago
            pw.Text('Método de pago: ${ticket.paymentMethod.toUpperCase()}',
                style: pw.TextStyle(
                    fontSize: 14,
                    fontWeight: pw.FontWeight.bold,
                    color: PdfColors.blueGrey800)),
            pw.Spacer(),

            // Footer
            pw.Divider(thickness: 1, color: PdfColors.grey400),
            pw.SizedBox(height: 8),
            pw.Center(
                child: pw.Text('¡Gracias por viajar con nosotros!',
                    style: pw.TextStyle(
                        fontSize: 12, color: PdfColors.grey700))),
          ],
        );
      },
    ),
  );

  await Printing.layoutPdf(
    onLayout: (format) async => pdf.save(),
  );
}

}
