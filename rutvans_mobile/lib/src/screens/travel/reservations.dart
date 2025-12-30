import 'package:flutter/material.dart';

class ReservasPage extends StatelessWidget {
  final List<int> asientos;

  const ReservasPage({super.key, required this.asientos});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Reservación Confirmada"),
        centerTitle: true,
        backgroundColor: Color(0xFFFF9800),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // Tarjeta de confirmación
              Card(
                elevation: 4,
                margin: const EdgeInsets.only(bottom: 30),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(20),
                  child: Column(
                    children: [
                      Container(
                        decoration: BoxDecoration(
                          color: Colors.green.withOpacity(0.1),
                          shape: BoxShape.circle,
                        ),
                        padding: const EdgeInsets.all(20),
                        child: const Icon(
                          Icons.check_circle,
                          size: 60,
                          color: Colors.green,
                        ),
                      ),
                      const SizedBox(height: 20),
                      const Text(
                        "¡Reserva Confirmada!",
                        style: TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 10),
                      Text(
                        "Número de asientos: ${asientos.length}",
                        style: const TextStyle(
                          fontSize: 16,
                          color: Colors.grey,
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              // Detalle de asientos
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                decoration: BoxDecoration(
                  color: Colors.grey.shade100,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.event_seat, color: Colors.deepOrangeAccent),
                    const SizedBox(width: 10),
                    Text(
                      "Asientos: ${asientos.join(", ")}",
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 30),

              // Detalles adicionales
ListTile(
  leading: Icon(Icons.receipt, color: Colors.deepOrangeAccent),
  title: Text("Número de reserva"),
  subtitle: Text("#${DateTime.now().millisecondsSinceEpoch.toString().padLeft(10, '0').substring(5)}"), 
),
              const Divider(height: 1),
 ListTile(
  leading: Icon(Icons.receipt, color: Colors.deepOrangeAccent),
  title: Text("Número de reserva"),
  subtitle: Text("#${DateTime.now().millisecondsSinceEpoch.toString().padLeft(10, '0').substring(5)}"), 
),

              const SizedBox(height: 40),

              // Botones de acción
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  ElevatedButton.icon(
                    onPressed: () => Navigator.pushNamedAndRemoveUntil(context, '/', (route) => false),
                    icon: const Icon(Icons.home),
                    label: const Text("Inicio"),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.white,
                      foregroundColor: Colors.deepOrangeAccent,
                      padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 20),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10),
                        side: const BorderSide(color: Colors.deepOrangeAccent),
                      ),
                    ),
                  ),
                  const SizedBox(width: 15),
                  ElevatedButton.icon(
                    onPressed: () {
                      // Acción para compartir reserva
                    },
                    icon: const Icon(Icons.share),
                    label: const Text("Compartir"),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.deepOrangeAccent,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 20),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}