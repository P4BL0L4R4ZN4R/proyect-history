import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:rutvans_mobile/src/screens/profile/freight_pay.dart';
import 'package:rutvans_mobile/src/services/freight_services/freight_service.dart';
import 'package:rutvans_mobile/src/screens/freight/freight_utils.dart';

class FreightRequests extends StatefulWidget {
  const FreightRequests({super.key});

  @override
  State<FreightRequests> createState() => _FreightRequestsState();
}

class _FreightRequestsState extends State<FreightRequests> {
  List<Map<String, dynamic>> _freightRequests = [];
  bool _isLoading = true;
  bool _hasError = false;

  @override
  void initState() {
    super.initState();
    _loadFreightRequests();
  }

  Future<void> _loadFreightRequests() async {
    setState(() {
      _isLoading = true;
      _hasError = false;
    });

    try {
      final requests = await RentService.getFreightRequests();
      setState(() {
        _freightRequests = requests;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _hasError = true;
        _isLoading = false;
      });
    }
  }

  Future<void> _cancelRequest(int id) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Cancelar Solicitud'),
        content: const Text('¿Estás seguro de que deseas cancelar esta solicitud de flete?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(false),
            child: const Text('No'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: primaryOrange,
              foregroundColor: Colors.white,
            ),
            onPressed: () => Navigator.of(context).pop(true),
            child: const Text('Sí, cancelar'),
          ),
        ],
      ),
    );

    if (confirmed == true) {
      final success = await RentService.cancelFreightRequest(id);
      if (!mounted) return;
      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Solicitud cancelada exitosamente')),
        );
        _loadFreightRequests();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Error al cancelar la solicitud')),
        );
      }
    }
  }

  String _formatStatus(String status) {
    switch (status) {
      case 'Pendiente':
        return 'Pendiente';
      case 'En progreso':
        return 'En progreso';
      case 'En progreso, pagado':
        return 'Pagado';
      case 'Completado':
        return 'Completado';
      default:
        return status;
    }
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'Pendiente':
        return mediumOrange;
      case 'En progreso':
        return Colors.blue;
      case 'En progreso, pagado':
        return Colors.deepPurple;
      case 'Completado':
        return Colors.green;
      default:
        return Colors.grey;
    }
  }

  IconData _getStatusIcon(String status) {
    switch (status) {
      case 'Pendiente':
        return Icons.hourglass_empty;
      case 'En progreso':
        return Icons.directions_car;
      case 'En progreso, pagado':
        return Icons.payment;
      case 'Completado':
        return Icons.check_circle;
      default:
        return Icons.help_outline;
    }
  }

  String _formatCurrency(double amount) {
    return '\$${amount.toStringAsFixed(2)}';
  }

  String _formatDateTime(String date, String time) {
    return '$date ${time.substring(0, 5)}';
  }

  String _formatDate(String dateString) {
    try {
      final date = DateTime.parse(dateString);
      return '${date.day}/${date.month}/${date.year} ${date.hour.toString().padLeft(2, '0')}:${date.minute.toString().padLeft(2, '0')}';
    } catch (e) {
      return dateString;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Mis Solicitudes de Flete'),
        backgroundColor: primaryOrange,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadFreightRequests,
          ),
        ],
      ),
      backgroundColor: lightGray,
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _hasError
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Icon(Icons.error_outline, size: 64, color: Colors.red),
                      const SizedBox(height: 16),
                      const Text(
                        'Error al cargar las solicitudes',
                        style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                      ),
                      const SizedBox(height: 8),
                      ElevatedButton(
                        onPressed: _loadFreightRequests,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: primaryOrange,
                          foregroundColor: Colors.white,
                        ),
                        child: const Text('Reintentar'),
                      ),
                    ],
                  ),
                )
              : _freightRequests.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(Icons.inbox, size: 64, color: darkGray),
                          const SizedBox(height: 16),
                          const Text(
                            'No tienes solicitudes de flete',
                            style: TextStyle(fontSize: 18, color: darkGray),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            'Crea una nueva solicitud en la página principal',
                            style: TextStyle(fontSize: 14, color: darkGray),
                            textAlign: TextAlign.center,
                          ),
                        ],
                      ),
                    )
                  : RefreshIndicator(
                      onRefresh: _loadFreightRequests,
                      child: ListView.builder(
                        padding: const EdgeInsets.all(16),
                        itemCount: _freightRequests.length,
                        itemBuilder: (context, index) {
                          final request = _freightRequests[index];
                          return _buildRequestCard(request);
                        },
                      ),
                    ),
    );
  }

  Widget _buildRequestCard(Map<String, dynamic> request) {
    final origin = request['origin'] is String
        ? jsonDecode(request['origin'])
        : request['origin'];
    final destination = request['destination'] is String
        ? jsonDecode(request['destination'])
        : request['destination'];

    final int id = int.tryParse(request['id'].toString()) ?? 0;
    final int passengers =
        int.tryParse(request['number_people'].toString()) ?? 0;
    final double amountValue =
        double.tryParse(request['amount']?.toString() ?? '') ?? 0.0;

    final canCancel = RentService.canCancelFreight(request);
    final canPay = RentService.canMakePayment(request);

    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      elevation: 4,
      color: Colors.white,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: Padding(
        padding: const EdgeInsets.all(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Estado y Acciones
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Chip(
                  backgroundColor: _getStatusColor(request['status']),
                  label: Row(
                    children: [
                      Icon(_getStatusIcon(request['status']),
                          color: Colors.white, size: 16),
                      const SizedBox(width: 6),
                      Text(
                        _formatStatus(request['status']),
                        style: const TextStyle(
                            color: Colors.white, fontSize: 12),
                      ),
                    ],
                  ),
                ),
                Row(
                  children: [
                    if (canCancel)
                      IconButton(
                        icon: const Icon(Icons.cancel,
                            color: Colors.redAccent, size: 22),
                        onPressed: () => _cancelRequest(id),
                      ),
                    if (canPay)
                      IconButton(
                        icon: const Icon(Icons.payment,
                            color: Colors.green, size: 22),
                        onPressed: ()  async {
                          final shouldReload = await Navigator.push(
                            context,
                            MaterialPageRoute(builder: (context) => FreightPayPage(request: request)),
                          );

                          if (shouldReload == true) {
                            _loadFreightRequests();
                          }
                        },
                      ),
                  ],
                ),
              ],
            ),
            const SizedBox(height: 12),

            // Folio
            _buildDetailRow('Folio', request['folio']?.toString() ?? 'N/A',
                Icons.receipt),

            // Origen
            _buildDetailRow(
                'Origen',
                '${origin['address'] ?? ''}, ${origin['city'] ?? ''}, ${origin['state'] ?? ''}, CP ${origin['zip'] ?? ''}',
                Icons.place),

            // Destino
            _buildDetailRow(
                'Destino',
                '${destination['address'] ?? ''}, ${destination['city'] ?? ''}, ${destination['state'] ?? ''}, CP ${destination['zip'] ?? ''}',
                Icons.flag),

            // Pasajeros
            _buildDetailRow(
                'Pasajeros', '$passengers personas', Icons.people),

            // Fechas
            _buildDetailRow('Fecha Inicio',
                _formatDateTime(request['start_date'], request['start_time']),
                Icons.calendar_today),
            _buildDetailRow('Fecha Fin',
                _formatDateTime(request['end_date'], request['end_time']),
                Icons.calendar_today),

            // Monto
            if (amountValue > 0)
              _buildDetailRow(
                  'Monto', _formatCurrency(amountValue), Icons.attach_money),

            // Fecha creación
            if (request['created_at'] != null)
              _buildDetailRow('Creado',
                  _formatDate(request['created_at']), Icons.access_time),
          ],
        ),
      ),
    );
  }

  Widget _buildDetailRow(String label, String value, IconData icon) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 20, color: primaryOrange),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label,
                    style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 13,
                        color: textGray)),
                const SizedBox(height: 2),
                Text(value,
                    style: const TextStyle(
                        fontSize: 14, color: darkGray),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
