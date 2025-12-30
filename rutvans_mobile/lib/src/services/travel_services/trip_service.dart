import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:rutvans_mobile/src/services/api_service.dart';

class TripService {
  Future<List<Map<String, dynamic>>> getRecentTrips() async {
    try {
      // Obtener el user_id desde SharedPreferences
      final prefs = await SharedPreferences.getInstance();
      final userId = prefs.getString('user_id');
      print('🔍 [TripService] user_id obtenido: $userId');

      if (userId == null) {
        print('❌ [TripService] No se encontró user_id en SharedPreferences');
        return [];
      }

      final url = '${ApiService.baseUrl}/client/recent-trips?user_id=$userId';
      print('🔍 [TripService] Enviando solicitud a: $url');
      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'User-Agent':
              'Mozilla/5.0 (compatible; Flutter-App/1.0; +https://your-app.com)',
          'X-Requested-With': 'XMLHttpRequest',
          'ngrok-skip-browser-warning': 'true',
        },
      );

      print('🔍 [TripService] Código de estado HTTP: ${response.statusCode}');
      print('🔍 [TripService] Headers de respuesta: ${response.headers}');
      print(
        '🔍 [TripService] Respuesta del servidor: ${response.body.substring(0, response.body.length > 200 ? 200 : response.body.length)}...',
      );

      if (response.statusCode == 200) {
        // Verificar si la respuesta es HTML
        if (response.body.startsWith('<!DOCTYPE html') ||
            response.body.contains('<html')) {
          print(
            '❌ [TripService] Respuesta es HTML, no JSON. Posible error de Ngrok (ERR_NGROK_6024).',
          );
          return [];
        }

        // Decodificar JSON de forma dinámica
        dynamic jsonResponse = jsonDecode(response.body);

        // Manejar tanto objeto como arreglo directo
        List<dynamic> trips = [];
        if (jsonResponse is Map<String, dynamic>) {
          trips = jsonResponse['data'] ?? [];
          print('🔍 [TripService] Usando clave "data" del objeto JSON');
        } else if (jsonResponse is List<dynamic>) {
          trips = jsonResponse;
          print('🔍 [TripService] Usando arreglo JSON directo');
        } else {
          print(
            '❌ [TripService] Formato JSON inesperado: ${jsonResponse.runtimeType}',
          );
          return [];
        }

        // Filtrar solo elementos que sean mapas válidos (por seguridad)
        final List<Map<String, dynamic>> validTrips = trips
            .where((trip) => trip is Map<String, dynamic>)
            .cast<Map<String, dynamic>>()
            .toList();

        print(
          '✅ [TripService] Obtenidos ${validTrips.length} viajes recientes válidos',
        );
        return validTrips;
      } else {
        print(
          '❌ [TripService] Error al obtener viajes: ${response.statusCode} - ${response.body}',
        );
        return [];
      }
    } catch (e) {
      print('❌ [TripService] Error al obtener viajes recientes: $e');
      return [];
    }
  }
}
