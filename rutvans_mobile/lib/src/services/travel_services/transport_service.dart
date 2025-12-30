import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:rutvans_mobile/src/services/api_service.dart';

class TransportApiService {
  // Cancelar una reserva por ID
  static Future<bool> cancelReservation(int reservationId) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');
      final response = await http.delete(
        Uri.parse('${ApiService.baseUrl}/client/reservations/$reservationId'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );
      return response.statusCode == 200 || response.statusCode == 204;
    } catch (e) {
      print('Error al cancelar la reserva: $e');
      return false;
    }
  }

  // Obtener información de la unidad
  static Future<Map<String, dynamic>?> getUnitInfo(String unitId) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final response = await http.get(
        Uri.parse('${ApiService.baseUrl}/client/units/$unitId'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'ngrok-skip-browser-warning': 'true',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
      return null;
    } catch (e) {
      print('Error al obtener unidad: $e');
      return null;
    }
  }

  // Obtener asientos ocupados
  static Future<List<int>> getOccupiedSeats(String unitId) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final response = await http.get(
        Uri.parse('${ApiService.baseUrl}/client/units/$unitId/occupied-seats'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'ngrok-skip-browser-warning': 'true',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return List<int>.from(data['occupied_seats'] ?? []);
      }
      return [];
    } catch (e) {
      print('Error al obtener asientos ocupados: $e');
      return [];
    }
  }

  // Reservar asientos
  static Future<int?> reserveSeats({
    required String unitId,
    required List<int> seats,
    required Map<String, dynamic> ticketDetails,
  }) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');
      final userId = prefs.getString('user_id');
      final requestBody = {
        'unit_id': unitId,
        'user_id': userId != null && int.tryParse(userId) != null
            ? int.parse(userId)
            : userId,
        'seats': seats,
        'ticket_details': ticketDetails,
      };
      print('🟠 Enviando reserva: ' + jsonEncode(requestBody));
      final response = await http.post(
        Uri.parse('${ApiService.baseUrl}/client/reservations'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode(requestBody),
      );
      print('🟡 Respuesta reserva status: ${response.statusCode}');
      print('🟡 Respuesta reserva body: ${response.body}');
      if (response.statusCode == 201) {
        final data = jsonDecode(response.body);
        return data['id'];
      } else if (response.statusCode == 400) {
        final data = jsonDecode(response.body);
        throw Exception(
          'Error al reservar: ${data['message'] ?? data.toString()}',
        );
      } else if (response.statusCode == 409) {
        final data = jsonDecode(response.body);
        throw Exception(
          'Algunos asientos ya están ocupados: ${data['conflicting_seats'] ?? ''}',
        );
      }
      return null;
    } catch (e) {
      print('🔴 Error al reservar asientos: $e');
      rethrow;
    }
  }
}
