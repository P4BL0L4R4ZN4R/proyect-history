import 'package:flutter/material.dart';
import '../payment/payment.dart'; // Asegúrate de importar tu pantalla de pago

const Color primaryOrange = Color(0xFFFF9800);
const Color darkOrange = Color(0xFFE65100);
const Color lightOrange = Color(0xFFFFE0B2);
const Color backgroundColor = Color(0xFFF5F5F5);

class ResumenViajePage extends StatelessWidget {
  final Map<String, int> detalle;
  final List<int> asientos;
  final int routeUnitScheduleId;
  final int rateId;
  final double precioPorAsiento;
  final int unitId;
  final int? reservationId;
  final DateTime travelDate;
final String origin;
final String destination;
final Duration duration;
final String driverName;

const ResumenViajePage({
  super.key,
  required this.detalle,
  required this.asientos,
  required this.routeUnitScheduleId,
  required this.rateId,
  required this.precioPorAsiento,
  required this.unitId,
  this.reservationId,
  required this.travelDate,
  required this.origin,
  required this.destination,
  required this.duration,
  required this.driverName,
});


  @override
  Widget build(BuildContext context) {
    // Ordenamos los asientos para mostrarlos de manera ordenada
    final sortedAsientos = List<int>.from(asientos)..sort();
    final asientosText = sortedAsientos.join(', ');
    // Debug: imprime los valores de los IDs de viaje
    print('[DEBUG ResumenViajePage] routeUnitScheduleId: 1'); // Valor fijo 1 para pruebas
    print('[DEBUG ResumenViajePage] rateId: ' + rateId.toString());
    print('[DEBUG ResumenViajePage] unitId: ' + unitId.toString());

    return Scaffold(
      backgroundColor: backgroundColor,
      appBar: AppBar(
        title: const Text('Resumen de tu Viaje'),
        backgroundColor: primaryOrange,
        elevation: 0,
        centerTitle: true,
        shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(
            bottom: Radius.circular(20),
          ),
        ),
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Tarjeta de resumen del viaje
              Card(
                elevation: 4,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(15),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Detalles del Viaje',
                        style: TextStyle(
                          fontSize: 20,
                          fontWeight: FontWeight.bold,
                          color: darkOrange,
                        ),
                      ),
                      const SizedBox(height: 16),
                     _buildInfoRow(Icons.calendar_today, 'Fecha:', '${travelDate.day}/${travelDate.month}/${travelDate.year}'),
_buildInfoRow(Icons.access_time, 'Hora:', '${travelDate.hour.toString().padLeft(2,'0')}:${travelDate.minute.toString().padLeft(2,'0')}'),
_buildInfoRow(Icons.place, 'Origen:', origin),
_buildInfoRow(Icons.place_outlined, 'Destino:', destination),
_buildInfoRow(Icons.timelapse, 'Duración:', '${duration.inHours}h ${duration.inMinutes.remainder(60)}m'),
_buildInfoRow(Icons.person, 'Conductor:', driverName),

                      _buildInfoRow(Icons.timelapse, 'Duración:', '2 horas 15 min'),
                      const SizedBox(height: 8),
                      const Divider(),
                      const SizedBox(height: 8),
                      _buildInfoRow(Icons.confirmation_number, 'Asientos:', asientosText),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Tarjeta de detalles de boletos
              Card(
                elevation: 4,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(15),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Detalles de Boletos',
                        style: TextStyle(
                          fontSize: 20,
                          fontWeight: FontWeight.bold,
                          color: darkOrange,
                        ),
                      ),
                      const SizedBox(height: 16),
                      if (detalle['pasajeros']! > 0)
                        _buildBoletoRow('Pasajero regular', detalle['pasajeros']!, 40),
                      if (detalle['estudiantes']! > 0)
                        _buildBoletoRow('Estudiante', detalle['estudiantes']!, 30),
                      if (detalle['adultosMayores']! > 0)
                        _buildBoletoRow('Adulto mayor', detalle['adultosMayores']!, 25),
                      const SizedBox(height: 8),
                      const Divider(),
                      const SizedBox(height: 8),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text(
                            'Total a pagar:',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          Text(
                            '\$${detalle['total']}',
                            style: const TextStyle(
                              fontSize: 22,
                              fontWeight: FontWeight.bold,
                              color: darkOrange,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Información adicional
              Card(
                elevation: 4,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(15),
                ),
                child: const Padding(
                  padding: EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Información Importante',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: darkOrange,
                        ),
                      ),
                      SizedBox(height: 10),
                      Text(
                        '• Presentar identificación para boletos de estudiante/adulto mayor\n'
                        '• Llegar 30 minutos antes de la salida\n'
                        '• No reembolsable\n'
                        '• Mascarilla opcional',
                        style: TextStyle(fontSize: 14),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 30),

              // Botones de acción
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => Navigator.pop(context),
                      style: OutlinedButton.styleFrom(
                        foregroundColor: darkOrange,
                        side: const BorderSide(color: darkOrange),
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      child: const Text('MODIFICAR'),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () {
                        print('[DEBUG Navegando a PagoPage] routeUnitScheduleId: 1'); // Valor fijo 1 para pruebas
                        print('[DEBUG Navegando a PagoPage] rateId: ' + rateId.toString());
                        print('[DEBUG Navegando a PagoPage] unitId: ' + unitId.toString());
                        
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => PagoPage(
                               asientos: sortedAsientos,
          detalleViaje: detalle, // Puede ser Map<String, dynamic>
          routeUnitScheduleId: routeUnitScheduleId,
          rateId: rateId,
          precioPorAsiento: precioPorAsiento,
          reservationId: reservationId ?? 0,
          unitId: unitId,
          origin: origin,
          destination: destination,
              duration: duration,
              travelDate: travelDate,
              driverName: driverName,
                            ),
                          ),
                        );
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: primaryOrange,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        elevation: 3,
                      ),
                      child: const Text('PAGAR'),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        children: [
          Icon(icon, size: 20, color: Colors.grey[600]),
          const SizedBox(width: 10),
          Text(
            label,
            style: TextStyle(
              fontSize: 16,
              color: Colors.grey[700],
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
              textAlign: TextAlign.end,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBoletoRow(String tipo, int cantidad, int precio) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            '$cantidad x $tipo',
            style: const TextStyle(fontSize: 16),
          ),
          Text(
            '\$${cantidad * precio}',
            style: const TextStyle(fontSize: 16),
          ),
        ],
      ),
    );
  }
}


















