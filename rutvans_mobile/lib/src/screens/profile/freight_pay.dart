import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:rutvans_mobile/src/services/freight_services/freight_service.dart';
import 'package:rutvans_mobile/src/screens/freight/freight_utils.dart';

class FreightPayPage extends StatefulWidget {
  final Map<String, dynamic> request;

  const FreightPayPage({super.key, required this.request});

  @override
  State<FreightPayPage> createState() => _FreightPayPageState();
}

class _FreightPayPageState extends State<FreightPayPage> {
  String? _selectedPaymentMethod;
  bool _isProcessing = false;

  Future<void> _confirmPayment() async {
    if (_selectedPaymentMethod == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Por favor selecciona un método de pago")),
      );
      return;
    }

    setState(() => _isProcessing = true);

    try {
      final id = widget.request['id'];
      
      // Usar el nuevo método de FreightService
      final success = await RentService.processFreightPayment(id, _selectedPaymentMethod!);

      if (success) {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text("Pago realizado con éxito"),
            backgroundColor: Colors.green,
          ),
        );
        Navigator.pop(context, true);
      } else {
        throw Exception('Error al procesar el pago');
      }
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text("Error: ${e.toString()}"),
          backgroundColor: Colors.red,
        ),
      );
    } finally {
      if (mounted) {
        setState(() => _isProcessing = false);
      }
    }
  }

  Widget _buildDetailRow(String label, String value, IconData icon) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: primaryOrange, size: 20),
          const SizedBox(width: 8),
          Expanded(
            child: RichText(
              text: TextSpan(
                text: "$label: ",
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                  color: textGray,
                  fontSize: 14,
                ),
                children: [
                  TextSpan(
                    text: value,
                    style: const TextStyle(
                      fontWeight: FontWeight.normal,
                      color: darkGray,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final origin = widget.request['origin'] is String
        ? jsonDecode(widget.request['origin'])
        : widget.request['origin'];
    final destination = widget.request['destination'] is String
        ? jsonDecode(widget.request['destination'])
        : widget.request['destination'];

    final double amount = double.tryParse(widget.request['amount'].toString()) ?? 0.0;

    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryOrange,
        title: const Text("Resumen de Pago"),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Card(
          elevation: 4,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  "Detalles de la Solicitud",
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: textGray,
                  ),
                ),
                const Divider(),

                _buildDetailRow("Folio", widget.request['folio'] ?? "N/A", Icons.receipt),
                _buildDetailRow("Origen", "${origin['address']}, ${origin['city']}", Icons.place),
                _buildDetailRow("Destino", "${destination['address']}, ${destination['city']}", Icons.flag),
                _buildDetailRow("Pasajeros", "${widget.request['number_people']} personas", Icons.people),
                _buildDetailRow("Monto", "\$${amount.toStringAsFixed(2)}", Icons.attach_money),
                _buildDetailRow("Estado actual", widget.request['status'], Icons.info),

                const SizedBox(height: 20),

                const Text(
                  "Selecciona un método de pago",
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                    color: textGray,
                  ),
                ),
                const SizedBox(height: 10),

                RadioListTile<String>(
                  title: const Text("Tarjeta de crédito / débito"),
                  value: "Tarjeta",
                  groupValue: _selectedPaymentMethod,
                  activeColor: primaryOrange,
                  onChanged: _isProcessing ? null : (value) {
                    setState(() => _selectedPaymentMethod = value);
                  },
                ),
                RadioListTile<String>(
                  title: const Text("Transferencia bancaria"),
                  value: "Transferencia",
                  groupValue: _selectedPaymentMethod,
                  activeColor: primaryOrange,
                  onChanged: _isProcessing ? null : (value) {
                    setState(() => _selectedPaymentMethod = value);
                  },
                ),
                RadioListTile<String>(
                  title: const Text("Pago en efectivo"),
                  value: "Efectivo",
                  groupValue: _selectedPaymentMethod,
                  activeColor: primaryOrange,
                  onChanged: _isProcessing ? null : (value) {
                    setState(() => _selectedPaymentMethod = value);
                  },
                ),

                const SizedBox(height: 20),

                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: primaryOrange,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      disabledBackgroundColor: Colors.grey,
                    ),
                    onPressed: _isProcessing ? null : _confirmPayment,
                    child: _isProcessing
                        ? const SizedBox(
                            width: 20,
                            height: 20,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              valueColor: AlwaysStoppedAnimation(Colors.white),
                            ),
                          )
                        : const Text(
                            "Confirmar Pago",
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                  ),
                )
              ],
            ),
          ),
        ),
      ),
    );
  }
}