import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:rutvans_mobile/src/services/api_service.dart'; // Importar ApiService

class RentService {
  static const String baseUrl = ApiService.baseUrl; // Usar baseUrl de ApiService

  // Obtener el token de autenticación desde ApiService
  static Future<String?> _getAuthToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }

  // Obtener el ID de usuario desde ApiService
  static Future<String?> _getUserId() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('user_id');
  }

  // Insertar solicitud de flete
  static Future<bool> insertRentRequest(Map<String, dynamic> request) async {
    try {
      final token = await _getAuthToken();
      final userId = await _getUserId();

      if (token == null || userId == null) {
        print('❌ Error: Usuario no autenticado');
        return false;
      }

      // Calcular monto aproximado
      final amount = _calculateApproximateAmount(request);

      // Preparar los datos para la API
      final Map<String, dynamic> apiRequest = {
        'start_date': request['start_date'],
        'end_date': request['end_date'],
        'start_time': request['start_time'],
        'end_time': request['end_time'],
        'user_id': int.parse(userId),
        'origin': jsonEncode(request['origin']),
        'destination': jsonEncode(request['destination']),
        'number_people': int.parse(request['passengers']),
        'status': 'Pendiente',
        'amount': amount,
      };

      print('🔍 [RentService] Enviando solicitud: ${jsonEncode(apiRequest)}');

      final response = await http.post(
        Uri.parse('$baseUrl/freights'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode(apiRequest),
      );

      print('🔍 [RentService] Status Code: ${response.statusCode}');
      print('🔍 [RentService] Response: ${response.body}');

      if (response.statusCode == 201) {
        print('✅ [RentService] Flete creado exitosamente');
        return true;
      } else {
        print('❌ [RentService] Error al crear flete: ${response.body}');
        return false;
      }
    } catch (e) {
      print('❌ [RentService] Error al insertar flete: $e');
      return false;
    }
  }

  // Obtener todas las solicitudes de flete del usuario
  static Future<List<Map<String, dynamic>>> getFreightRequests() async {
    try {
      final token = await _getAuthToken();
      final userId = await _getUserId();

      if (token == null || userId == null) {
        print('❌ Error: Usuario no autenticado');
        return [];
      }

      final response = await http.get(
        Uri.parse('$baseUrl/freights?user_id=$userId'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      print('🔍 [RentService] Get Status Code: ${response.statusCode}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> responseData = jsonDecode(response.body);
        
        if (responseData['success'] == true) {
          final List<dynamic> data = responseData['data'];
          print('✅ [RentService] ${data.length} fletes obtenidos');
          return data.map((item) => item as Map<String, dynamic>).toList();
        } else {
          print('❌ [RentService] Error en respuesta: ${responseData['message']}');
          return [];
        }
      } else {
        print('❌ [RentService] Error HTTP: ${response.body}');
        return [];
      }
    } catch (e) {
      print('❌ [RentService] Error al obtener fletes: $e');
      return [];
    }
  }

  // Cancelar una solicitud de flete
  static Future<bool> cancelFreightRequest(int freightId) async {
    try {
      final token = await _getAuthToken();

      if (token == null) {
        print('❌ Error: Usuario no autenticado');
        return false;
      }

      final response = await http.delete(
        Uri.parse('$baseUrl/freights/$freightId'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      print('🔍 [RentService] Cancel Status Code: ${response.statusCode}');
      print('🔍 [RentService] Cancel Response: ${response.body}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> responseData = jsonDecode(response.body);
        if (responseData['success'] == true) {
          print('✅ [RentService] Flete cancelado exitosamente');
          return true;
        }
      }
      
      print('❌ [RentService] Error al cancelar flete: ${response.body}');
      return false;
    } catch (e) {
      print('❌ [RentService] Error al cancelar flete: $e');
      return false;
    }
  }

  // Obtener un flete específico por ID
  static Future<Map<String, dynamic>?> getFreightById(int freightId) async {
    try {
      final token = await _getAuthToken();

      if (token == null) {
        print('❌ Error: Usuario no autenticado');
        return null;
      }

      final response = await http.get(
        Uri.parse('$baseUrl/freights/$freightId'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      print('🔍 [RentService] Get by ID Status Code: ${response.statusCode}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> responseData = jsonDecode(response.body);
        
        if (responseData['success'] == true) {
          print('✅ [RentService] Flete obtenido exitosamente');
          return responseData['data'] as Map<String, dynamic>;
        } else {
          print('❌ [RentService] Error en respuesta: ${responseData['message']}');
          return null;
        }
      } else {
        print('❌ [RentService] Error HTTP: ${response.body}');
        return null;
      }
    } catch (e) {
      print('❌ [RentService] Error al obtener flete: $e');
      return null;
    }
  }

  // Actualizar estado de un flete (para pagos, etc.)
  static Future<bool> updateFreightStatus(int freightId, String newStatus) async {
    try {
      final token = await _getAuthToken();

      if (token == null) {
        print('❌ Error: Usuario no autenticado');
        return false;
      }

      final response = await http.put(
        Uri.parse('$baseUrl/freights/$freightId/status'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode({
          'status': newStatus,
        }),
      );

      print('🔍 [RentService] Update Status Code: ${response.statusCode}');
      print('🔍 [RentService] Update Response: ${response.body}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> responseData = jsonDecode(response.body);
        if (responseData['success'] == true) {
          print('✅ [RentService] Estado actualizado exitosamente');
          return true;
        }
      }
      
      print('❌ [RentService] Error al actualizar estado: ${response.body}');
      return false;
    } catch (e) {
      print('❌ [RentService] Error al actualizar estado: $e');
      return false;
    }
  }

  // NUEVO MÉTODO: Procesar pago de flete (mover desde freight_pay.dart)
  static Future<bool> processFreightPayment(int freightId, String paymentMethod) async {
    try {
      final token = await _getAuthToken();

      if (token == null) {
        print('❌ Error: Usuario no autenticado');
        return false;
      }

      final response = await http.put(
        Uri.parse('$baseUrl/freights/$freightId/status'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode({
          'status': 'En progreso, Pagado',
          'payment_method': paymentMethod,
        }),
      );

      print('🔍 [RentService] Payment Status Code: ${response.statusCode}');
      print('🔍 [RentService] Payment Response: ${response.body}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> responseData = jsonDecode(response.body);
        if (responseData['success'] == true) {
          print('✅ [RentService] Pago procesado exitosamente');
          return true;
        }
      } else if (response.statusCode == 422) {
        final errorData = jsonDecode(response.body);
        throw Exception(errorData['message'] ?? 'Datos inválidos');
      }
      
      print('❌ [RentService] Error al procesar pago: ${response.body}');
      return false;
    } catch (e) {
      print('❌ [RentService] Error al procesar pago: $e');
      rethrow;
    }
  }

  // Cálculo aproximado del monto (esto será reemplazado con cálculo real basado en distancia)
  static double _calculateApproximateAmount(Map<String, dynamic> request) {
    // Cálculo simple basado en número de pasajeros y tiempo estimado
    // Esto es temporal - luego se calculará basado en distancia real
    
    final int passengers = int.parse(request['passengers']);
    final String startTime = request['start_time'];
    final String endTime = request['end_time'];
    
    // Calcular horas estimadas
    final start = _parseTime(startTime);
    final end = _parseTime(endTime);
    final durationHours = _calculateDurationHours(start, end);
    
    // Tarifas base (esto será configurable)
    const double baseRate = 100.0;
    const double ratePerHour = 50.0;
    const double ratePerPassenger = 10.0;
    
    // Cálculo simple
    double amount = baseRate + 
                   (durationHours * ratePerHour) + 
                   (passengers * ratePerPassenger);
    
    print('💰 [RentService] Monto calculado: \$$amount');
    return amount;
  }

  // Métodos auxiliares para cálculo de tiempo
  static DateTime _parseTime(String timeString) {
    final parts = timeString.split(':');
    final now = DateTime.now();
    return DateTime(now.year, now.month, now.day, int.parse(parts[0]), int.parse(parts[1]));
  }

  static double _calculateDurationHours(DateTime start, DateTime end) {
    final difference = end.difference(start);
    return difference.inMinutes / 60.0;
  }

  // Verificar si un flete puede ser pagado (está "En progreso")
  static bool canMakePayment(Map<String, dynamic> freight) {
    return freight['status'] == 'En progreso';
  }

  // Verificar si un flete puede ser cancelado (está "Pendiente")
  static bool canCancelFreight(Map<String, dynamic> freight) {
    return freight['status'] == 'Pendiente';
  }
}