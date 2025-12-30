import 'package:flutter/material.dart';
import 'package:rutvans_mobile/src/constants/constant_colors.dart';
import 'package:rutvans_mobile/src/services/profile_services/history_service.dart';
import 'dart:convert';

class RateTravelScreen extends StatefulWidget {
  final Map<String, dynamic> travel;
  final VoidCallback onRatingSubmitted;

  const RateTravelScreen({
    super.key,
    required this.travel,
    required this.onRatingSubmitted,
  });

  @override
  RateTravelScreenState createState() => RateTravelScreenState();
}

class RateTravelScreenState extends State<RateTravelScreen> {
  final HistoryService _historyService = HistoryService();
  int _rating = 0;
  String _reportText = '';
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    // Inicializar con valores existentes si los hay
    _rating = widget.travel['rating'] ?? 0;
    _reportText = widget.travel['report'] ?? '';
  }

  ImageProvider _getDriverImage(String? image) {
    try {
      if (image != null && image.startsWith('data:image')) {
        return MemoryImage(base64Decode(image.split(',').last));
      }
      return const AssetImage('assets/images/driver1.jpg');
    } catch (e) {
      return const AssetImage('assets/images/driver1.jpg');
    }
  }

  Future<void> _submitRating() async {
    if (_rating == 0 && _reportText.isEmpty) {
      _showSnackBar('Por favor, califica el viaje o escribe un reporte');
      return;
    }

    setState(() {
      _isSubmitting = true;
    });

    try {
      await _historyService.updateTravelRatingAndReport(
        widget.travel['_id'],
        _rating == 0 ? null : _rating,
        _reportText,
      );

      if (mounted) {
        _showSnackBar(
          _rating > 0
              ? 'Calificación guardada exitosamente'
              : 'Reporte guardado exitosamente',
          isSuccess: true,
        );

        widget.onRatingSubmitted();
        Navigator.pop(context);
      }
    } catch (e) {
      if (mounted) {
        _showSnackBar('Error al guardar: $e');
      }
    } finally {
      if (mounted) {
        setState(() {
          _isSubmitting = false;
        });
      }
    }
  }

  void _showSnackBar(String message, {bool isSuccess = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: isSuccess
            ? ConstantColors.colorSuccess
            : ConstantColors.colorError,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      ),
    );
  }

  Widget _buildDriverInfo() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: ConstantColors.colorCardBackground,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        children: [
          CircleAvatar(
            radius: 30,
            backgroundImage: _getDriverImage(widget.travel['driver']?['image']),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  widget.travel['driver']?['name'] ?? 'Unknown',
                  style: const TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 16,
                  ),
                ),
                Text(
                  widget.travel['driver']?['vehicle'] ?? 'No vehicle',
                  style: const TextStyle(fontSize: 12),
                ),
                const SizedBox(height: 4),
                Text(
                  widget.travel['driver']?['description'] ?? 'No description',
                  style: const TextStyle(fontSize: 14),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTravelDetails() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: ConstantColors.colorCardBackground,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Detalles del Viaje',
            style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Icon(Icons.place, color: Colors.green, size: 20),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  widget.travel['origin'] ?? 'Unknown',
                  style: const TextStyle(fontSize: 14),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            children: [
              Icon(Icons.location_on, color: Colors.red, size: 20),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  widget.travel['destination'] ?? 'Unknown',
                  style: const TextStyle(fontSize: 14),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            children: [
              Icon(
                Icons.calendar_today,
                color: ConstantColors.colorIcon,
                size: 16,
              ),
              const SizedBox(width: 8),
              Text(
                '${widget.travel['date'] ?? 'Unknown'} • ${widget.travel['time'] ?? 'Unknown'}',
                style: const TextStyle(fontSize: 14),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            children: [
              Icon(
                Icons.monetization_on,
                color: ConstantColors.colorIcon,
                size: 16,
              ),
              const SizedBox(width: 8),
              Text(
                widget.travel['amount'] ?? 'Unknown',
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildRatingSection() {
    final isCanceled = widget.travel['status'] == 'canceled';

    if (isCanceled) {
      return const SizedBox.shrink();
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Califica tu viaje',
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
        ),
        const SizedBox(height: 8),
        const Text(
          '¿Cómo calificarías tu experiencia con el conductor?',
          style: TextStyle(fontSize: 14, color: Colors.grey),
        ),
        const SizedBox(height: 16),
        Center(
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: List.generate(5, (index) {
              return IconButton(
                icon: Icon(
                  index < _rating ? Icons.star : Icons.star_border,
                  color: Colors.amber,
                  size: 40,
                ),
                onPressed: () {
                  setState(() {
                    _rating = index + 1;
                  });
                },
              );
            }),
          ),
        ),
        const SizedBox(height: 8),
        Center(
          child: Text(
            _rating == 0
                ? 'Toca una estrella para calificar'
                : 'Calificación: $_rating/5',
            style: TextStyle(
              fontSize: 14,
              color: _rating == 0 ? Colors.grey : Colors.amber[700],
              fontWeight: FontWeight.w500,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildReportSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Reportar problema',
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
        ),
        const SizedBox(height: 8),
        const Text(
          'Describe cualquier problema que hayas tenido durante el viaje (opcional)',
          style: TextStyle(fontSize: 14, color: Colors.grey),
        ),
        const SizedBox(height: 12),
        TextField(
          decoration: InputDecoration(
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(8),
              borderSide: BorderSide(color: ConstantColors.colorInputBorder),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(8),
              borderSide: BorderSide(color: ConstantColors.colorInputBorder2),
            ),
            hintText: 'Describe el problema...',
            contentPadding: const EdgeInsets.all(16),
          ),
          maxLines: 4,
          onChanged: (value) {
            setState(() {
              _reportText = value;
            });
          },
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    final isCanceled = widget.travel['status'] == 'canceled';

    return Scaffold(
      backgroundColor: ConstantColors.colorPageBackground,
      appBar: AppBar(
        title: Text(
          isCanceled ? 'Reportar Viaje' : 'Calificar Viaje',
          style: const TextStyle(
            fontWeight: FontWeight.w600,
            fontSize: 20,
            color: ConstantColors.colorAppBarText,
          ),
        ),
        backgroundColor: ConstantColors.colorAppBarBackground,
        elevation: 0,
        centerTitle: true,
        iconTheme: const IconThemeData(color: ConstantColors.colorAppBarIcon),
      ),
      body: _isSubmitting
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildDriverInfo(),
                  const SizedBox(height: 20),
                  _buildTravelDetails(),
                  const SizedBox(height: 20),
                  if (!isCanceled) ...[
                    _buildRatingSection(),
                    const SizedBox(height: 20),
                  ],
                  _buildReportSection(),
                  const SizedBox(height: 30),
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton(
                          onPressed: () => Navigator.pop(context),
                          style: OutlinedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            side: BorderSide(
                              color: ConstantColors.colorCardBorder,
                            ),
                          ),
                          child: Text(
                            'Cancelar',
                            style: TextStyle(
                              color: ConstantColors.colorCardText,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: ElevatedButton(
                          onPressed: _submitRating,
                          style: ElevatedButton.styleFrom(
                            backgroundColor:
                                ConstantColors.colorButtonBackground,
                            padding: const EdgeInsets.symmetric(vertical: 16),
                          ),
                          child: Text(
                            _isSubmitting ? 'Guardando...' : 'Guardar',
                            style: TextStyle(
                              color: ConstantColors.colorButtonText,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
    );
  }
}
