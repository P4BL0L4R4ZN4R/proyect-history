import 'dart:convert';
import 'package:http/http.dart' as http;

class ShippingService {
  // Cambia esta URL por la de tu API real o configura para emulador/dispositivo
  final String _baseUrl;

  ShippingService({String? baseUrl})
      : _baseUrl = baseUrl ?? 'http://10.0.2.2:8000'; // Por defecto para emulador Android

  Future<Map<String, dynamic>> insertShippingRequest(Map<String, dynamic> requestData) async {
    final url = Uri.parse('$_baseUrl/api/shipments');

    try {
      final response = await http.post(
        url,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(requestData),
      );

      final Map<String, dynamic> responseBody = jsonDecode(response.body);

      if (response.statusCode == 201) {
        // Registro exitoso
        return {
          'success': true,
          'message': responseBody['message'] ?? 'Envío registrado correctamente',
          'data': responseBody['shipment'],
        };
      } else if (response.statusCode == 422) {
        // Error de validación
        return {
          'success': false,
          'message': responseBody['message'] ?? 'Error de validación',
          'errors': responseBody['errors'] ?? {},
        };
      } else {
        // Otros errores
        return {
          'success': false,
          'message': 'Error del servidor: ${response.statusCode}',
          'details': responseBody,
        };
      }
    } catch (e) {
      return {
        'success': false,
        'message': 'Error al conectar con el servidor',
        'error': e.toString(),
      };
    }
  }
}