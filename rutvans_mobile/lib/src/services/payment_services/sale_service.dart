import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:rutvans_mobile/src/services/api_service.dart';

// Mapeo de métodos de pago
const paymentMethods = {'efectivo': 1, 'qr': 2, 'tarjeta': 6, 'billetera': 3};

class Sale {
  final int userId;
  final int routeUnitScheduleId;
  final int rateId;
  final String data; // ✅ Cambiado a String para guardar como JSON
  final double amount;
  final int paymentId;
  final List<int> seats; // ✅ Agregamos los asientos

  Sale({
    required this.userId,
    required this.routeUnitScheduleId,
    required this.rateId,
    required this.data, // ✅ Ahora es String (JSON)
    required this.amount,
    required this.paymentId,
    required this.seats, // ✅ Asientos requeridos
  });

  Map<String, dynamic> toJson() {
    return {
      'user_id': userId,
      'route_unit_schedule_id': routeUnitScheduleId,
      'rate_id': rateId,
      'data': data, // ✅ Se envía como string JSON
      'amount': amount,
      'payment_id': paymentId,
      'status': 'Completado',
      'seats': seats, // ✅ Incluir asientos
    };
  }
}

// ✅ NUEVA CLASE: Para manejar tarjetas de crédito de forma representativa
class CreditCard {
  final String cardNumber;
  final String expiryDate;
  final String cvv;
  final String cardHolderName;
  final String type; // visa, mastercard, etc.

  CreditCard({
    required this.cardNumber,
    required this.expiryDate,
    required this.cvv,
    required this.cardHolderName,
    required this.type,
  });

  Map<String, dynamic> toJson() => {
    'card_number': _maskCardNumber(cardNumber),
    'expiry_date': expiryDate,
    'card_holder': cardHolderName,
    'type': type,
  };

  String _maskCardNumber(String number) {
    if (number.length <= 4) return number;
    return '**** **** **** ${number.substring(number.length - 4)}';
  }

  // Validación básica de tarjeta
  bool isValid() {
    if (cardNumber.length != 16) return false;
    if (cvv.length != 3) return false;
    if (!RegExp(r'^\d{2}/\d{2}$').hasMatch(expiryDate)) return false;
    return true;
  }

  // Detectar tipo de tarjeta
  static String detectCardType(String number) {
    if (number.startsWith('4')) return 'Visa';
    if (number.startsWith('5')) return 'Mastercard';
    if (number.startsWith('34') || number.startsWith('37'))
      return 'American Express';
    if (number.startsWith('6')) return 'Discover';
    return 'Otra';
  }
}

class SaleApiService {
  static Future<Map<String, dynamic>> createSale(Sale sale) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final requestBody = sale.toJson();
      print('🛒 CREANDO VENTA - Datos a enviar:');
      print('URL: ${ApiService.baseUrl}/client/sales');
      print('Body: ${jsonEncode(requestBody)}');

      final response = await http.post(
        Uri.parse('${ApiService.baseUrl}/client/sales'),
        headers: {
          'Content-Type': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
        body: jsonEncode(requestBody),
      );

      print('📋 RESPUESTA DEL SERVIDOR:');
      print('Status: ${response.statusCode}');
      print('Body: ${response.body}');

      if (response.statusCode == 201) {
        final responseData = jsonDecode(response.body);
        final data = responseData['data'] ?? {};
        return {'success': true, 'folio': data['folio'], 'sale': data};
      } else {
        String errorMsg = 'Error desconocido';
        try {
          final decoded = jsonDecode(response.body);
          errorMsg = decoded is Map
              ? (decoded['message'] ?? decoded.toString())
              : decoded.toString();
        } catch (_) {
          errorMsg = response.body;
        }
        print('❌ Error en venta: ${response.statusCode} - $errorMsg');
        return {
          'success': false,
          'message': errorMsg,
          'statusCode': response.statusCode,
        };
      }
    } catch (e) {
      print('💥 Excepción al crear venta: $e');
      return {
        'success': false,
        'message': 'Error de conexión: ${e.toString()}',
      };
    }
  }

  // ✅ NUEVO MÉTODO: Procesar pago con tarjeta (representativo)
  static Future<Map<String, dynamic>> processCardPayment({
    required CreditCard card,
    required double amount,
    required Map<String, dynamic> saleData,
  }) async {
    try {
      print('💳 PROCESANDO PAGO CON TARJETA (Simulación)');
      print('Tarjeta: ${card.toJson()}');
      print('Monto: \$$amount');

      // Simular procesamiento de pago
      await Future.delayed(const Duration(seconds: 2));

      // Validar tarjeta
      if (!card.isValid()) {
        return {
          'success': false,
          'message': 'Datos de tarjeta inválidos',
          'error_code': 'INVALID_CARD',
        };
      }

      // Simular diferentes resultados
      final random = DateTime.now().millisecond % 10;

      if (random == 0) {
        // Simular rechazo de tarjeta (10% de probabilidad)
        return {
          'success': false,
          'message': 'Tarjeta rechazada. Contacte a su banco.',
          'error_code': 'CARD_DECLINED',
        };
      } else if (random == 1) {
        // Simular fondos insuficientes (10% de probabilidad)
        return {
          'success': false,
          'message': 'Fondos insuficientes',
          'error_code': 'INSUFFICIENT_FUNDS',
        };
      }

      // Simular pago exitoso (80% de probabilidad)
      print('✅ PAGO SIMULADO EXITOSO');

      // Crear la venta después del pago exitoso
      final saleResult = await createSale(
        Sale(
          userId: saleData['user_id'],
          routeUnitScheduleId: saleData['route_unit_schedule_id'],
          rateId: saleData['rate_id'],
          data: saleData['data'],
          amount: amount,
          paymentId: paymentMethods['tarjeta']!,
          seats: saleData['seats'] ?? [], // ✅ Asientos desde saleData
        ),
      );

      if (saleResult['success'] != true) {
        return {
          'success': false,
          'message': saleResult['message'] ?? 'Error al registrar la venta',
        };
      }

      return {
        'success': true,
        'message': 'Pago procesado exitosamente',
        'folio': saleResult['folio'],
        'transaction_id': 'TXN_${DateTime.now().millisecondsSinceEpoch}',
        'card_last_four': card.cardNumber.substring(card.cardNumber.length - 4),
        'card_type': card.type,
      };
    } catch (e) {
      print('💥 Error procesando pago con tarjeta: $e');
      return {
        'success': false,
        'message': 'Error al procesar el pago: ${e.toString()}',
      };
    }
  }

  // ✅ NUEVO MÉTODO: Validar tarjeta (sin procesar pago)
  static Map<String, dynamic> validateCard(CreditCard card) {
    try {
      if (card.cardNumber.isEmpty) {
        return {'valid': false, 'message': 'Número de tarjeta requerido'};
      }

      if (card.cardNumber.length != 16) {
        return {
          'valid': false,
          'message': 'Número de tarjeta debe tener 16 dígitos',
        };
      }

      if (!RegExp(r'^\d+$').hasMatch(card.cardNumber)) {
        return {
          'valid': false,
          'message': 'Número de tarjeta solo puede contener números',
        };
      }

      if (!RegExp(r'^\d{2}/\d{2}$').hasMatch(card.expiryDate)) {
        return {
          'valid': false,
          'message': 'Fecha de expiración debe ser MM/AA',
        };
      }

      // Validar que la fecha no esté expirada
      final parts = card.expiryDate.split('/');
      final month = int.tryParse(parts[0]);
      final year = int.tryParse(parts[1]);

      if (month == null || year == null || month < 1 || month > 12) {
        return {'valid': false, 'message': 'Fecha de expiración inválida'};
      }

      final now = DateTime.now();
      final currentYear = now.year % 100;
      final currentMonth = now.month;

      if (year < currentYear || (year == currentYear && month < currentMonth)) {
        return {'valid': false, 'message': 'Tarjeta expirada'};
      }

      if (card.cvv.length != 3) {
        return {'valid': false, 'message': 'CVV debe tener 3 dígitos'};
      }

      if (card.cardHolderName.isEmpty) {
        return {'valid': false, 'message': 'Nombre del titular requerido'};
      }

      return {
        'valid': true,
        'message': 'Tarjeta válida',
        'card_type': card.type,
      };
    } catch (e) {
      return {
        'valid': false,
        'message': 'Error validando tarjeta: ${e.toString()}',
      };
    }
  }

  static Future<Map<String, dynamic>> getRecentSales(int userId) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final response = await http.get(
        Uri.parse('${ApiService.baseUrl}/client/sales/recent?user_id=$userId'),
        headers: {if (token != null) 'Authorization': 'Bearer $token'},
      );

      if (response.statusCode == 200) {
        return {'success': true, 'sales': jsonDecode(response.body)};
      } else {
        return {'success': false, 'message': 'Error al obtener historial'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Error de conexión'};
    }
  }
}
