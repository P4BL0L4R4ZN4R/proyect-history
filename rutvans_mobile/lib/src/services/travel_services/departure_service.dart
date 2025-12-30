import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:rutvans_mobile/src/services/api_service.dart';

class DepartureService {
  /// Obtiene los destinos disponibles para un origen específico
  Future<List<String>> getAvailableDestinations(String origin) async {
    try {
      final url =
          '${ApiService.baseUrl}/client/available-destinations?origin=${Uri.encodeComponent(origin)}';
      print('🔍 [DepartureService] Obteniendo destinos para: $origin');
      print('🔍 [DepartureService] URL: $url');

      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'User-Agent': 'Mozilla/5.0 (compatible; Flutter-App/1.0)',
          'X-Requested-With': 'XMLHttpRequest',
          'ngrok-skip-browser-warning': 'true',
        },
      );

      if (response.statusCode == 200) {
        final Map<String, dynamic> jsonResponse = jsonDecode(response.body);
        final List<dynamic> destinations = jsonResponse['data'] ?? [];
        return destinations.cast<String>();
      } else {
        print('❌ [DepartureService] Error al obtener destinos: ${response.statusCode}');
        return [];
      }
    } catch (e) {
      print('❌ [DepartureService] Error al obtener destinos: $e');
      return [];
    }
  }

  /// Obtiene los horarios de rutas, opcionalmente filtrando por origen y destino
  Future<List<Map<String, dynamic>>> getRouteUnitSchedules({
    String? origin,
    String? destination,
  }) async {
    try {
      var url = '${ApiService.baseUrl}/client/route-unit-schedules';
      if (origin != null || destination != null) {
        final queryParams = <String, String>{};
        if (origin != null) queryParams['origin'] = origin;
        if (destination != null) queryParams['destination'] = destination;
        url = Uri.parse(url).replace(queryParameters: queryParams).toString();
      }
      print('🔍 [DepartureService] Enviando solicitud a: $url');

      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'User-Agent': 'Mozilla/5.0 (compatible; Flutter-App/1.0)',
          'X-Requested-With': 'XMLHttpRequest',
          'ngrok-skip-browser-warning': 'true',
        },
      );

      if (response.statusCode == 200) {
        if (response.body.startsWith('<!DOCTYPE html') ||
            response.body.contains('<html')) {
          print('❌ [DepartureService] Respuesta es HTML, no JSON.');
          return [];
        }

        final Map<String, dynamic> jsonResponse = jsonDecode(response.body);
        final dynamic data = jsonResponse['data'];

        // Manejar ambos casos: data es Map con 'schedules' o List directamente
        List<Map<String, dynamic>> schedules;
        if (data is List) {
          schedules = data.cast<Map<String, dynamic>>();
        } else if (data is Map && data['schedules'] != null) {
          schedules = (data['schedules'] as List).cast<Map<String, dynamic>>();
        } else {
          schedules = [];
        }

        print('✅ [DepartureService] Obtenidos ${schedules.length} horarios.');
        return schedules;
      } else {
        print('❌ [DepartureService] Error al obtener horarios: ${response.statusCode} - ${response.body}');
        return [];
      }
    } catch (e) {
      print('❌ [DepartureService] Error al obtener horarios: $e');
      return [];
    }
  }

  /// Obtiene los orígenes disponibles desde todos los horarios
  Future<List<String>> getAvailableOrigins() async {
    try {
      final url = '${ApiService.baseUrl}/client/route-unit-schedules';
      print('🔍 [DepartureService] Obteniendo orígenes desde: $url');

      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'User-Agent': 'Mozilla/5.0 (compatible; Flutter-App/1.0)',
          'X-Requested-With': 'XMLHttpRequest',
          'ngrok-skip-browser-warning': 'true',
        },
      );

      if (response.statusCode == 200) {
        final Map<String, dynamic> jsonResponse = jsonDecode(response.body);
        final dynamic data = jsonResponse['data'];

        // Extraer available_origins si existe
        if (data is Map && data['available_origins'] != null) {
          final List<dynamic> availableOrigins = data['available_origins'];
          return availableOrigins.cast<String>();
        }

        return [];
      } else {
        print('❌ [DepartureService] Error HTTP ${response.statusCode}');
        return [];
      }
    } catch (e) {
      print('❌ [DepartureService] Error obteniendo orígenes disponibles: $e');
      return [];
    }
  }
}
