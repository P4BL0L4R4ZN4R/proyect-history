import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:rutvans_mobile/src/services/travel_services/trip_service.dart';
import 'package:shared_preferences/shared_preferences.dart';

const Color primaryOrange = Color(0xFFFF9800);
const Color pressedOrange = Color(0xFFE65100);

class Viaje {
  final String id;
  final String origin;
  final String destination;
  final DateTime date;
  final String time;
  final String amount;
  final String status;
  final int rating;
  final String driverName;
  final DateTime createdAt;
  final DateTime updatedAt;

  Viaje({
    required this.id,
    required this.origin,
    required this.destination,
    required this.date,
    required this.time,
    required this.amount,
    required this.status,
    required this.rating,
    required this.driverName,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Viaje.fromJson(Map<String, dynamic> json) {
    return Viaje(
      id: json['id']?.toString() ?? '',
      origin: json['origin']?.toString() ?? '',
      destination: json['destination']?.toString() ?? '',
      date: DateTime.tryParse(json['date']?.toString() ?? '') ?? DateTime.now(),
      time: json['time']?.toString() ?? '',
      amount: json['amount']?.toString() ?? '0.00',
      status: json['status']?.toString() ?? '',
      rating: int.tryParse(json['rating']?.toString() ?? '0') ?? 0,
      driverName: json['driver_name']?.toString() ?? 'No disponible',
      createdAt: DateTime.tryParse(json['created_at']?.toString() ?? '') ?? DateTime.now(),
      updatedAt: DateTime.tryParse(json['updated_at']?.toString() ?? '') ?? DateTime.now(),
    );
  }
}

class ViajeDetails extends StatelessWidget {
  final Viaje viaje;

  const ViajeDetails({super.key, required this.viaje});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
      child: Stack(
        children: [
          Card(
            elevation: 8,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            child: SingleChildScrollView(
              child: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [primaryOrange.withOpacity(0.2), Colors.white],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: primaryOrange, width: 2),
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(vertical: 10),
                      decoration: BoxDecoration(
                        color: primaryOrange,
                        borderRadius: const BorderRadius.vertical(top: Radius.circular(14)),
                      ),
                      child: Center(
                        child: Text(
                          'Detalles de Viaje',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                          ),
                        ),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(12),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildInfoRow(Icons.location_on, 'Origen', viaje.origin),
                          const Divider(color: Colors.grey, height: 10),
                          _buildInfoRow(Icons.location_on, 'Destino', viaje.destination),
                          const Divider(color: Colors.grey, height: 10),
                          _buildInfoRow(Icons.calendar_today, 'Fecha', DateFormat('yyyy-MM-dd').format(viaje.date)),
                          const Divider(color: Colors.grey, height: 10),
                          _buildInfoRow(Icons.access_time, 'Hora', viaje.time),
                          const Divider(color: Colors.grey, height: 10),
                          _buildInfoRow(Icons.person, 'Conductor', viaje.driverName),
                          const Divider(color: Colors.grey, height: 10),
                          _buildInfoRow(Icons.attach_money, 'Monto', viaje.amount),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
          Positioned(
            left: -10,
            top: 40,
            child: Container(
              width: 20,
              height: 20,
              decoration: BoxDecoration(
                color: Colors.white,
                shape: BoxShape.circle,
                border: Border.all(color: primaryOrange, width: 2),
              ),
            ),
          ),
          Positioned(
            right: -10,
            top: 40,
            child: Container(
              width: 20,
              height: 20,
              decoration: BoxDecoration(
                color: Colors.white,
                shape: BoxShape.circle,
                border: Border.all(color: primaryOrange, width: 2),
              ),
            ),
          ),
          Positioned(
            right: 8,
            top: 8,
            child: IconButton(
              icon: Icon(Icons.close, color: Colors.white),
              onPressed: () => Navigator.pop(context),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        children: [
          Icon(icon, color: primaryOrange, size: 20),
          const SizedBox(width: 8),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: TextStyle(fontSize: 12, color: Colors.grey[600]),
              ),
              Text(
                value,
                style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.black87),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class TripWidget extends StatefulWidget {
  const TripWidget({super.key});

  @override
  State<TripWidget> createState() => _TripWidgetState();
}

class _TripWidgetState extends State<TripWidget> {
  late Future<List<Viaje>> _viajesFuture;
  final TripService _tripService = TripService();
  String? _userName;
  String? _pressedTripId;  // Track which trip is pressed

  @override
  void initState() {
    super.initState();
    _loadUserName();
    _viajesFuture = _tripService.getRecentTrips().then((trips) => trips.map((json) => Viaje.fromJson(json)).toList());
  }

  Future<void> _loadUserName() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      _userName = prefs.getString('user_name') ?? 'Usuario';
    });
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<Viaje>>(
      future: _viajesFuture,
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Center(child: CircularProgressIndicator());
        } else if (snapshot.hasError) {
          return const Center(child: Text('Error al cargar viajes'));
        } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
          return Center(child: Text('$_userName, aún no tiene viajes recientes registrados'));
        } else {
          final viajes = snapshot.data!;
          return Container(
            decoration: BoxDecoration(
              border: Border.all(color: primaryOrange, width: 2),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Column(
              children: [
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(vertical: 12),
                  decoration: BoxDecoration(
                    color: primaryOrange,
                    borderRadius: const BorderRadius.vertical(top: Radius.circular(8)),
                  ),
                  child: const Center(
                    child: Text(
                      'Viajes recientes',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                  ),
                ),
                ...viajes.map((viaje) {
                  final isPressed = _pressedTripId == viaje.id;
                  return Container(
                    padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
                    decoration: BoxDecoration(
                      border: Border(top: BorderSide(color: primaryOrange, width: 1)),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('${viaje.origin} - ${viaje.destination}'),
                        GestureDetector(
                          onTapDown: (_) => setState(() => _pressedTripId = viaje.id),
                          onTapUp: (_) => setState(() => _pressedTripId = null),
                          onTapCancel: () => setState(() => _pressedTripId = null),
                          onTap: () {
                            showModalBottomSheet(
                              context: context,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
                              ),
                              builder: (context) => ViajeDetails(viaje: viaje),
                            );
                          },
                          child: MouseRegion(
                            cursor: SystemMouseCursors.click,
                            child: AnimatedContainer(
                              duration: const Duration(milliseconds: 100),
                              transform: Matrix4.identity()..scale(isPressed ? 0.95 : 1.0),
                              child: Row(
                                children: [
                                  Icon(
                                    Icons.info_outline,
                                    color: isPressed ? pressedOrange : primaryOrange,
                                  ),
                                  const SizedBox(width: 6),
                                  const Text(
                                    'Detalles',
                                    style: TextStyle(
                                      color: Colors.black,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  );
                }),
              ],
            ),
          );
        }
      },
    );
  }
}