import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:rutvans_mobile/src/services/api_service.dart';

class HistoryService {
  Future<List<Map<String, dynamic>>> fetchTravelsHistory() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final userId = prefs.getString('user_id');
      print('🔍 [HistoryService] user_id: $userId');
      if (userId == null) {
        print('❌ [HistoryService] No se encontró user_id en SharedPreferences');
        return []; // ← FIX: Retorna vacío en lugar de throw
      }

      final headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'User-Agent':
            'Mozilla/5.0 (compatible; Flutter-App/1.0; +https://your-app.com)', // ← FIX: Simula app
        'X-Requested-With': 'XMLHttpRequest',
        'ngrok-skip-browser-warning': 'true', // ← FIX: Evita HTML de Ngrok
      };

      // 🔹 Si quieres historial COMPLETO (no solo 3 recientes), cambia a '/client/travel-history'
      final url =
          '${ApiService.baseUrl}/client/travel-history?user_id=$userId'; // Corregido con prefijo /client/
      print('🔍 [HistoryService] URL: $url');

      final response = await http.get(Uri.parse(url), headers: headers);

      print('🔍 [HistoryService] Status Code: ${response.statusCode}');
      print('🔍 [HistoryService] Headers: ${response.headers}');
      print(
        '🔍 [HistoryService] Response: ${response.body.substring(0, response.body.length > 200 ? 200 : response.body.length)}...',
      );

      if (response.statusCode == 200) {
        // ✅ FIX: Verificar HTML (ya lo tenías, pero ahora retorna [] en lugar de throw)
        if (response.body.startsWith('<!DOCTYPE html') ||
            response.body.contains('<html')) {
          print(
            '❌ [HistoryService] Respuesta es HTML, no JSON. Posible error de Ngrok (ERR_NGROK_6024).',
          );
          return []; // ← FIX: Lista vacía, no crash
        }

        // ✅ FIX: Decodificar dinámico (array directo, como en recent-trips)
        dynamic jsonResponse = jsonDecode(response.body);
        List<dynamic> travels = [];
        if (jsonResponse is Map<String, dynamic>) {
          travels = jsonResponse['data'] ?? []; // Si es {"data": [...]}
          print('🔍 [HistoryService] Usando clave "data" del objeto JSON');
        } else if (jsonResponse is List<dynamic>) {
          travels = jsonResponse; // Array directo [...]
          print('🔍 [HistoryService] Usando arreglo JSON directo');
        } else {
          print(
            '❌ [HistoryService] Formato JSON inesperado: ${jsonResponse.runtimeType}',
          );
          return [];
        }

        // ✅ FIX: Filtrar mapas válidos (seguridad)
        final List<Map<String, dynamic>> validTravels = travels
            .where((travel) => travel is Map<String, dynamic>)
            .cast<Map<String, dynamic>>()
            .toList();

        print(
          '✅ [HistoryService] Obtenidos ${validTravels.length} viajes válidos',
        );

        // Tu mapeo actual (sin cambios, pero usa validTravels)
        return validTravels.map((travel) {
          return {
            '_id': travel['id'].toString(),
            'origin': travel['origin']?.toString() ?? 'No origin',
            'destination':
                travel['destination']?.toString() ?? 'No destination',
            'date': travel['date']?.toString() ?? 'No date',
            'time': travel['time']?.toString() ?? 'No time',
            'amount': travel['amount']?.toString() ?? 'No amount',
            'status': travel['status']?.toString() ?? 'No status',
            'driver': {
              'name': travel['driver_name']?.toString() ?? 'No name',
              'image': 'assets/images/driver1.jpg',
              'description': 'Conductor experimentado',
              'vehicle': 'Sprinter',
            },
            'rating': travel['rating']?.toInt() ?? 0,
            'report': travel['report']?.toString() ?? '',
          };
        }).toList();
      } else {
        print(
          '❌ [HistoryService] Error HTTP: ${response.statusCode} - ${response.body}',
        );
        return []; // ← FIX: Retorna vacío, no throw
      }
    } catch (e) {
      print('❌ [HistoryService] Error general: $e');
      return []; // ← FIX: Siempre retorna [] para graceful handling en UI
    }
  }

  Future<void> updateTravelRatingAndReport(
    String travelId,
    int? rating,
    String report,
  ) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      if (token == null) {
        print(
          '❌ [HistoryService] No se encontró auth_token en SharedPreferences',
        );
        throw Exception('No se encontró auth_token en SharedPreferences');
      }

      final body = <String, dynamic>{};
      if (rating != null) {
        body['passenger_rating'] = rating;
      }
      if (report.isNotEmpty) {
        body['report'] = report;
      }

      // 🔹 Agrega headers anti-Ngrok aquí también si es necesario
      final headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
        'ngrok-skip-browser-warning': 'true', // ← FIX opcional
      };

      final patchUrl = '${ApiService.baseUrl}/client/travel-history/$travelId';
      final response = await http.patch(
        Uri.parse(patchUrl),
        headers: headers,
        body: jsonEncode(body),
      );

      print('🔍 [HistoryService] PATCH URL: $patchUrl');
      print('🔍 [HistoryService] Status Code: ${response.statusCode}');
      print('🔍 [HistoryService] Request Body: ${jsonEncode(body)}');
      print('🔍 [HistoryService] Response: ${response.body}');

      if (response.statusCode != 200) {
        // ✅ FIX: Chequeo HTML en PATCH también
        if (response.body.startsWith('<!DOCTYPE html') ||
            response.body.contains('<html')) {
          print('❌ [HistoryService] Respuesta PATCH es HTML');
          throw Exception('Error del servidor: Respuesta inválida');
        }
        throw Exception(
          'Error al actualizar la calificación: ${response.statusCode} - ${response.body}',
        );
      }

      print('✅ [HistoryService] Viaje actualizado correctamente');
    } catch (e) {
      print('❌ [HistoryService] Error updating rating: $e');
      throw Exception('Error updating rating and report: $e');
    }
  }
}
