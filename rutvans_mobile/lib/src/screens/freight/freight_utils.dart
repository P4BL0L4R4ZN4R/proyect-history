import 'package:flutter/material.dart';

// Colores y constantes
const Color primaryOrange = Color(0xFFFF9800);
const Color lightOrange = Color(0xFFFFE0B2);
const Color mediumOrange = Color(0xFFFFB74D);
const Color textGray = Color(0xFF454545);
const Color lightGray = Color(0xFFF5F5F5);
const Color darkGray = Color(0xFF757575);

// Utilidades
String formatTime(TimeOfDay time) {
  return '${time.hour.toString().padLeft(2, '0')}:${time.minute.toString().padLeft(2, '0')}';
}

String formatAddress(Map<String, dynamic> details) {
  if (details.isEmpty) return 'Ubicación seleccionada';
  return '${details['address'] ?? ''}, ${details['neighborhood'] ?? ''}, ${details['city'] ?? ''}';
}