import 'package:flutter/material.dart';
import 'freight_utils.dart';

class LocationCard extends StatelessWidget {
  final String title;
  final Map<String, dynamic> details;
  final Color color;

  const LocationCard({
    super.key,
    required this.title,
    required this.details,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color.withValues(alpha: 0.3), width: 2),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.1),
            blurRadius: 5,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(
                title == 'Origen' ? Icons.place : Icons.flag,
                color: color,
                size: 20,
              ),
              const SizedBox(width: 8),
              Text(
                title,
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                  color: color,
                  fontSize: 16,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          _buildDetailRow('📍 Dirección:', details['address'] ?? 'No disponible'),
          _buildDetailRow('🏘️ Colonia:', details['neighborhood'] ?? 'No disponible'),
          _buildDetailRow('🏙️ Ciudad:', details['city'] ?? 'No disponible'),
          _buildDetailRow('🗺️ Estado:', details['state'] ?? 'No disponible'),
          _buildDetailRow('📮 CP:', details['postal_code'] ?? 'No disponible'),
          const SizedBox(height: 8),
          Text(
            'Coordenadas: ${details['lat']?.toStringAsFixed(6)}, ${details['lng']?.toStringAsFixed(6)}',
            style: TextStyle(
              fontSize: 12,
              color: darkGray,
              fontStyle: FontStyle.italic,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 100,
            child: Text(
              label,
              style: TextStyle(
                fontWeight: FontWeight.w600,
                color: darkGray,
                fontSize: 14,
              ),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: TextStyle(
                color: textGray,
                fontSize: 14,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
        ],
      ),
    );
  }
}