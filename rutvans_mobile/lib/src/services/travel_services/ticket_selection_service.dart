import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:rutvans_mobile/src/services/api_service.dart';

class TicketSelectionService {
  /// Obtiene los horarios disponibles con capacidad de unidad
  static Future<List<Map<String, dynamic>>> getRouteUnitSchedules({
    String? origin,
    String? destination,
  }) async {
    var url = '${ApiService.baseUrl}/client/route-unit-schedules';
    if (origin != null || destination != null) {
      final queryParams = <String, String>{};
      if (origin != null) queryParams['origin'] = origin;
      if (destination != null) queryParams['destination'] = destination;
      url = Uri.parse(url).replace(queryParameters: queryParams).toString();
    }
    final response = await http.get(
      Uri.parse(url),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'ngrok-skip-browser-warning': 'true',
      },
    );
    if (response.statusCode == 200) {
      final Map<String, dynamic> jsonResponse = jsonDecode(response.body);
      final List<dynamic> schedules = jsonResponse['data'] ?? [];
      return schedules.cast<Map<String, dynamic>>();
    } else {
      throw Exception('Error obteniendo horarios: ${response.statusCode}');
    }
  }

  /// Obtiene las tarifas (rates) disponibles
  static Future<List<Map<String, dynamic>>> getRates() async {
    final response = await http.get(
      Uri.parse('${ApiService.baseUrl}/client/rates'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'ngrok-skip-browser-warning': 'true',
      },
    );
    if (response.statusCode == 200) {
      final Map<String, dynamic> jsonResponse = jsonDecode(response.body);
      final List<dynamic> rates = jsonResponse['data'] ?? [];
      return rates.cast<Map<String, dynamic>>();
    } else {
      throw Exception('Error obteniendo tarifas: ${response.statusCode}');
    }
  }

  /// Obtiene los asientos ocupados para una unidad y horario
  static Future<List<dynamic>> getOccupiedSeats(
    String unitId,
    int scheduleId,
  ) async {
    final url =
        '${ApiService.baseUrl}/client/units/$unitId/occupied-seats?schedule_id=$scheduleId';
    print('🔍 [getOccupiedSeats] Obteniendo asientos ocupados...');
    print('🔍 [getOccupiedSeats] URL: $url');
    print('🔍 [getOccupiedSeats] unitId: $unitId, scheduleId: $scheduleId');

    final response = await http.get(
      Uri.parse(url),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'ngrok-skip-browser-warning': 'true',
      },
    );

    print('🔍 [getOccupiedSeats] Status Code: ${response.statusCode}');
    print('🔍 [getOccupiedSeats] Response Body: ${response.body}');
    if (response.statusCode == 200 &&
        response.headers['content-type'] != null &&
        response.headers['content-type']!.contains('application/json')) {
      final decoded = jsonDecode(response.body);
      print('🔍 [getOccupiedSeats] Decoded response: $decoded');
      if (decoded is Map && decoded.containsKey('occupied_seats')) {
        final occupiedSeats = decoded['occupied_seats'] is List
            ? List.from(decoded['occupied_seats'])
            : [];
        print(
          '🔍 [getOccupiedSeats] Asientos ocupados encontrados: $occupiedSeats',
        );
        return occupiedSeats;
      } else if (decoded is List) {
        print('🔍 [getOccupiedSeats] Response es List directa: $decoded');
        return decoded;
      } else {
        // Si la respuesta es HTML, lanza excepción clara
        if (response.body.startsWith('<!DOCTYPE html')) {
          throw Exception(
            'El backend respondió HTML. Verifica que el endpoint y ngrok estén activos.',
          );
        }
        throw Exception(
          'Respuesta JSON inesperada (asientos ocupados): ${response.body}',
        );
      }
    } else if (response.statusCode == 200 &&
        response.body.startsWith('<!DOCTYPE html')) {
      throw Exception(
        'El backend respondió HTML. Verifica que el endpoint y ngrok estén activos.',
      );
    } else {
      throw Exception(
        'Error obteniendo asientos ocupados: status ${response.statusCode}, body: ${response.body}',
      );
    }
  }
}
