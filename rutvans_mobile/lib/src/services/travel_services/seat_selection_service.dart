import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:rutvans_mobile/src/services/api_service.dart';

class SeatSelectionService {
  /// Obtiene los asientos ocupados para una unidad y horario
  static Future<List<dynamic>> getOccupiedSeats(
    String unitId,
    int scheduleId,
  ) async {
    final url =
        '${ApiService.baseUrl}/client/units/$unitId/occupied-seats?schedule_id=$scheduleId';
    print('🔍 [SeatSelectionService.getOccupiedSeats] URL: $url');
    print(
      '🔍 [SeatSelectionService.getOccupiedSeats] unitId: $unitId, scheduleId: $scheduleId',
    );

    final response = await http.get(
      Uri.parse(url),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'ngrok-skip-browser-warning': 'true',
      },
    );

    print(
      '🔍 [SeatSelectionService.getOccupiedSeats] Status Code: ${response.statusCode}',
    );
    print(
      '🔍 [SeatSelectionService.getOccupiedSeats] Response Body: ${response.body}',
    );

    if (response.statusCode == 200 &&
        response.headers['content-type'] != null &&
        response.headers['content-type']!.contains('application/json')) {
      final decoded = jsonDecode(response.body);
      print(
        '🔍 [SeatSelectionService.getOccupiedSeats] Decoded response: $decoded',
      );

      if (decoded is Map && decoded.containsKey('occupied_seats')) {
        final occupiedSeats = decoded['occupied_seats'] is List
            ? List.from(decoded['occupied_seats'])
            : [];
        print(
          '🔍 [SeatSelectionService.getOccupiedSeats] Asientos ocupados encontrados: $occupiedSeats',
        );
        return occupiedSeats;
      } else if (decoded is List) {
        print(
          '🔍 [SeatSelectionService.getOccupiedSeats] Response es List directa: $decoded',
        );
        return decoded;
      } else {
        print(
          '❌ [SeatSelectionService.getOccupiedSeats] Respuesta JSON inesperada: ${response.body}',
        );
        throw Exception(
          'Respuesta JSON inesperada (asientos ocupados): ${response.body}',
        );
      }
    } else {
      print(
        '❌ [SeatSelectionService.getOccupiedSeats] Error ${response.statusCode}: ${response.body}',
      );
      throw Exception(
        'Error obteniendo asientos ocupados: status ${response.statusCode}, body: ${response.body}',
      );
    }
  }

  /// Genera la lista de asientos disponibles según la capacidad
  static List<int> getSeatNumbers(int unitCapacity) {
    return List.generate(unitCapacity, (index) => index + 1);
  }
}
