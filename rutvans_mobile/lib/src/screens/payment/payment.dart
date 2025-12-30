import 'dart:convert';
import 'dart:async';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:flutter_stripe/flutter_stripe.dart' hide Card;
import 'package:http/http.dart' as http;
import 'package:qr_flutter/qr_flutter.dart';
import 'package:rutvans_mobile/src/services/payment_services/sale_service.dart';
import 'ticket_page.dart';

// Mapeo de métodos de pago
const paymentMethods = {
  'tarjeta': 6, // 3 = tarjeta en tu BD
  'qr': 2, // 2 = qr en tu BD
  'billetera': 3, // 4 = billetera en tu BD
};

class PagoPage extends StatefulWidget {
  final List<int> asientos;
  final Map<String, dynamic> detalleViaje;
  final int routeUnitScheduleId;
  final int rateId;
  final double precioPorAsiento;
  final int unitId;
  final int reservationId;
  final String origin;
  final String destination;
  final Duration duration;
  final String driverName;
  final DateTime travelDate;
  final String? passengerName; // ✅ Nuevo campo para nombre del pasajero

  const PagoPage({
    super.key,
    required this.asientos,
    required this.detalleViaje,
    required this.routeUnitScheduleId,
    required this.rateId,
    required this.precioPorAsiento,
    required this.unitId,
    required this.reservationId,
    required this.origin,
    required this.destination,
    required this.duration,
    required this.driverName,
    required this.travelDate,
    this.passengerName, // ✅ Nuevo parámetro
  });

  @override
  State<PagoPage> createState() => _PagoPageState();
}

class _PagoPageState extends State<PagoPage> {
  String? _metodoPagoSeleccionado;
  bool _pagoRealizado = false;
  bool _procesandoPago = false;
  bool _mostrarQR = false; // ✅ Nuevo estado para mostrar QR
  String? _mensajeError;
  int? _userId;
  String? _folioVenta;
  String? _qrData; // ✅ Datos para el QR

  // ✅ CONFIGURACIÓN DE STRIPE
  static const String backendUrl =
      "https://d2e20f966491.ngrok-free.app/api"; // CAMBIA ESTO
  static const String stripePublishableKey =
      "pk_test_51SCrD919HZqLwOqTUBQtDywLYFrKqH3tOcnsZl2Fe8eWaKeqT5DhketBOb3GQoj9bvjNQEwvFoKZoveMG1okfsZS00WhZqy7bV";
  static const String stripeMerchantIdentifier = "merchant.com.rutvans.app";

  double get total => widget.asientos.length * widget.precioPorAsiento;

  @override
  void initState() {
    super.initState();
    _obtenerUserId();
    _configurarStripe();
    _debugPrintDatos();
  }

  void _debugPrintDatos() {
    debugPrint('---[DEBUG PagoPage] Datos recibidos---');
    debugPrint('Asientos: ${widget.asientos}');
    debugPrint('Detalle viaje: ${widget.detalleViaje}');
    debugPrint('routeUnitScheduleId: ${widget.routeUnitScheduleId}');
    debugPrint('rateId: ${widget.rateId}');
    debugPrint('precioPorAsiento: ${widget.precioPorAsiento}');
    debugPrint('unitId: ${widget.unitId}');
    debugPrint('reservationId: ${widget.reservationId}');
    debugPrint('origin: ${widget.origin}');
    debugPrint('destination: ${widget.destination}');
    debugPrint('duracion: ${widget.duration}');
    debugPrint('nombre del conductor: ${widget.driverName}');
    debugPrint('--------------------------------------');
  }

  Future<void> _configurarStripe() async {
    try {
      Stripe.publishableKey = stripePublishableKey;
      Stripe.merchantIdentifier = stripeMerchantIdentifier;
      await Stripe.instance.applySettings();
      debugPrint('✅ Stripe configurado correctamente');
    } catch (e) {
      debugPrint('❌ Error configurando Stripe: $e');
    }
  }

  Future<void> _obtenerUserId() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      _userId = prefs.getInt('userId') ?? 0;
    });
  }

  void _seleccionarMetodoPago(String metodo) {
    setState(() {
      _metodoPagoSeleccionado = metodo;
      _mensajeError = null;
      _mostrarQR = false; // ✅ Ocultar QR cuando se cambia método
    });
  }

  // ✅ Función para crear Payment Intent
  Future<Map<String, dynamic>> _crearPaymentIntent() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      debugPrint('🔄 Creando Payment Intent...');
      debugPrint('URL: $backendUrl/create-payment-intent');
      debugPrint('Amount: ${(total * 100).round()} centavos');

      final requestBody = {
        'amount': (total * 100).round(),
        'currency': 'mxn',
        'metadata': {
          'user_id': _userId,
          'route_unit_schedule_id': widget.routeUnitScheduleId,
          'asientos': widget.asientos.join(','),
          'reservation_id': widget.reservationId,
        },
      };

      debugPrint('📤 Payment Intent Request: ${jsonEncode(requestBody)}');

      final response = await http.post(
        Uri.parse('$backendUrl/create-payment-intent'),
        headers: {
          'Content-Type': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
        body: jsonEncode(requestBody),
      );

      debugPrint('📥 Payment Intent Response: ${response.statusCode}');
      debugPrint('📥 Payment Intent Body: ${response.body}');

      if (response.statusCode == 200) {
        final result = jsonDecode(response.body);

        if (result['clientSecret'] == null) {
          debugPrint('❌ clientSecret es null en la respuesta');
          debugPrint('❌ Respuesta completa: $result');
          throw Exception('No se pudo obtener clientSecret del servidor');
        }

        debugPrint('✅ Payment Intent creado exitosamente');
        return result;
      } else {
        debugPrint('❌ Error HTTP en Payment Intent: ${response.statusCode}');
        debugPrint('❌ Body del error: ${response.body}');
        throw Exception(
          'Error del servidor al crear payment intent: ${response.statusCode}',
        );
      }
    } catch (e) {
      debugPrint('💥 Error creando payment intent: $e');
      throw Exception('Error creando payment intent: $e');
    }
  }

  // ✅ Función para crear el JSON en el formato EXACTO que necesitas
  String _crearDatosViajeJson() {
    // Obtener el método de pago en texto
    String metodoPagoTexto = '';
    switch (_metodoPagoSeleccionado) {
      case 'qr':
        metodoPagoTexto = 'qr';
        break;
      case 'billetera':
        metodoPagoTexto = 'efectivo';
        break;
      case 'tarjeta':
        metodoPagoTexto = 'tarjeta';
        break;
      default:
        metodoPagoTexto = 'efectivo';
    }

    // Obtener nombre del pasajero (usar el proporcionado o uno por defecto)
    String passengerName = widget.passengerName ?? "Sin nombre";

    // Obtener fecha y hora actual
    DateTime now = DateTime.now();
    String fecha =
        "${now.year}-${now.month.toString().padLeft(2, '0')}-${now.day.toString().padLeft(2, '0')}";
    String hora =
        "${now.hour.toString().padLeft(2, '0')}:${now.minute.toString().padLeft(2, '0')}";

    // ✅ Crear la estructura EXACTA que necesitas
    final datosViaje = {
      "tickets": [
        {
          "passenger_name": passengerName,
          "fare_type": "Adulto", // Puedes ajustar esto según tu lógica
          "seat_number": widget.asientos.isNotEmpty ? widget.asientos.first : 0,
          "origin": widget.origin,
          "destination": widget.destination,
          "date": fecha,
          "departure_time": hora,
          "base_price": widget.precioPorAsiento,
          "discount": 0,
          "total": widget.precioPorAsiento,
          "payment_method": metodoPagoTexto,
        },
      ],
      "total": total,
      "taxes": 0,
      "grand_total": total,
    };

    debugPrint('📋 JSON creado: ${jsonEncode(datosViaje)}');
    return jsonEncode(datosViaje);
  }

  // ✅ Función para generar datos del QR
  String _generarDatosQR() {
    final qrData = {
      "empresa": "RUTVANS",
      "monto": total,
      "moneda": "MXN",
      "referencia": "REF-${DateTime.now().millisecondsSinceEpoch}",
      "fecha": DateTime.now().toIso8601String(),
      "origen": widget.origin,
      "destino": widget.destination,
      "asientos": widget.asientos,
    };
    return jsonEncode(qrData);
  }

  // ✅ Función mejorada para validar datos
  void _validarDatosAntesDePago() {
    if (_userId == null || _userId == 0) {
      throw Exception(
        'ID de usuario no válido. Por favor, inicie sesión nuevamente.',
      );
    }

    if (widget.routeUnitScheduleId == null) {
      throw Exception('Información del viaje no válida.');
    }

    if (widget.rateId == null) {
      throw Exception('Información de tarifa no válida.');
    }

    if (widget.asientos.isEmpty) {
      throw Exception('No hay asientos seleccionados.');
    }

    if (total <= 0) {
      throw Exception('Monto total no válido.');
    }

    final paymentMethod = paymentMethods[_metodoPagoSeleccionado];
    if (paymentMethod == null) {
      throw Exception('Método de pago no válido.');
    }
  }

  // ✅ Función para mostrar QR y procesar pago después de escanear
  void _mostrarCodigoQR() {
    setState(() {
      _qrData = _generarDatosQR();
      _mostrarQR = true;
    });
  }

  // ✅ Función para simular pago exitoso después de escanear QR
  void _simularPagoQRExitoso() {
    setState(() {
      _mostrarQR = false;
      _procesandoPago = true;
    });
    _procesarPagoQR();
  }

  // ✅ Función separada para procesar pago QR
  Future<void> _procesarPagoQR() async {
    try {
      debugPrint('🔄 Procesando pago QR...');

      // ✅ CONVERTIR detalleViaje a string JSON con formato exacto
      String datosViajeJson = _crearDatosViajeJson();

      debugPrint('📋 JSON data a guardar: $datosViajeJson');

      final sale = Sale(
        userId: _userId!,
        routeUnitScheduleId: widget.routeUnitScheduleId,
        rateId: widget.rateId,
        data: datosViajeJson, // ✅ Enviar como string JSON con formato exacto
        amount: total,
        paymentId: paymentMethods[_metodoPagoSeleccionado]!,
        seats: widget.asientos, // ✅ Asientos requeridos por backend
      );

      debugPrint('📦 Sale creada: ${sale.toJson()}');

      final saleResult = await SaleApiService.createSale(sale);
      debugPrint('📥 Respuesta de SaleApiService: $saleResult');

      if (saleResult['success'] != true) {
        throw Exception(saleResult['message'] ?? 'Error al procesar el pago');
      }

      setState(() {
        _pagoRealizado = true;
        _folioVenta = saleResult['folio'];
        _procesandoPago = false;
      });

      // ✅ Navegar a TicketPage después de pago exitoso
      await _navegarATicketPage();
    } catch (e) {
      debugPrint('💥 Error en procesarPagoQR: $e');
      setState(() {
        _procesandoPago = false;
        _mensajeError =
            'Error en el pago: ${e.toString().replaceAll('Exception: ', '')}';
      });
    }
  }

  // ✅ Nueva función para navegar a TicketPage
  Future<void> _navegarATicketPage() async {
    if (mounted) {
      final ticket = Ticket(
        serviceType: "pasaje",
        paymentMethod: _metodoPagoSeleccionado ?? "desconocido",
        asientos: widget.asientos,
        detalleViaje: widget.detalleViaje,
        folio: _folioVenta!,
        total: total,
        origin: widget.origin,
        destination: widget.destination,
        duration: widget.duration,
        driverName: widget.driverName,
        travelDate: widget.travelDate,
      );

      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => TicketPage(ticket: ticket)),
      );
    }
  }

  Future<void> _realizarPago() async {
    if (_metodoPagoSeleccionado == null) {
      setState(() => _mensajeError = 'Por favor, selecciona un método de pago');
      return;
    }

    // ✅ Si es QR, mostrar el código QR en lugar de procesar inmediatamente
    if (_metodoPagoSeleccionado == 'qr') {
      _mostrarCodigoQR();
      return;
    }

    setState(() {
      _procesandoPago = true;
      _mensajeError = null;
    });

    try {
      // ✅ Validar datos antes de procesar el pago
      _validarDatosAntesDePago();

      if (_metodoPagoSeleccionado == 'tarjeta') {
        // ✅ 1. Crear Payment Intent
        debugPrint('🔄 Paso 1: Creando Payment Intent...');
        final paymentIntent = await _crearPaymentIntent();

        final clientSecret = paymentIntent['clientSecret'];
        if (clientSecret == null) {
          throw Exception('No se pudo procesar el pago. Intente nuevamente.');
        }

        debugPrint('✅ Payment Intent ID: ${paymentIntent['id']}');

        // ✅ 2. Configurar Payment Sheet
        debugPrint('🔄 Paso 2: Configurando Payment Sheet...');
        await Stripe.instance.initPaymentSheet(
          paymentSheetParameters: SetupPaymentSheetParameters(
            paymentIntentClientSecret: clientSecret,
            merchantDisplayName: 'RUTVANS',
            applePay: const PaymentSheetApplePay(merchantCountryCode: 'MX'),
            googlePay: const PaymentSheetGooglePay(
              merchantCountryCode: 'MX',
              testEnv: true,
              currencyCode: 'MXN',
            ),
            style: ThemeMode.light,
            appearance: const PaymentSheetAppearance(
              colors: PaymentSheetAppearanceColors(primary: Color(0xFFFF9800)),
            ),
            allowsDelayedPaymentMethods: false,
          ),
        );

        // ✅ 3. Mostrar Payment Sheet
        debugPrint('🔄 Paso 3: Mostrando Payment Sheet...');
        await Stripe.instance.presentPaymentSheet();
        debugPrint('✅ Payment Sheet completado exitosamente');

        // ✅ 4. Preparar datos para la venta con formato exacto
        debugPrint('🔄 Paso 4: Preparando datos de la venta...');

        String datosViajeJson = _crearDatosViajeJson();

        final saleData = {
          'user_id': _userId,
          'route_unit_schedule_id': widget.routeUnitScheduleId,
          'rate_id': widget.rateId,
          'data': datosViajeJson,
          'amount': total,
          'payment_id': paymentMethods[_metodoPagoSeleccionado],
          'status': 'Completado',
          'payment_intent_id': paymentIntent['id'],
          'seats': widget.asientos.join(','),
        };

        debugPrint('📦 Datos a enviar al backend:');
        debugPrint(jsonEncode(saleData));

        // ✅ 5. Confirmar pago con tu backend
        debugPrint('🔄 Paso 5: Confirmando pago con backend...');
        final prefs = await SharedPreferences.getInstance();
        final token = prefs.getString('token');

        final requestBody = {
          'payment_intent_id': paymentIntent['id'],
          'sale_data': saleData,
        };

        final confirmResponse = await http
            .post(
              Uri.parse('$backendUrl/confirm-payment'),
              headers: {
                'Content-Type': 'application/json',
                if (token != null) 'Authorization': 'Bearer $token',
              },
              body: jsonEncode(requestBody),
            )
            .timeout(const Duration(seconds: 30));

        debugPrint('📥 RESPUESTA HTTP: ${confirmResponse.statusCode}');
        debugPrint('📥 BODY COMPLETO: ${confirmResponse.body}');

        if (confirmResponse.statusCode != 200) {
          debugPrint('❌ ERROR HTTP ${confirmResponse.statusCode}');

          try {
            final errorResponse = jsonDecode(confirmResponse.body);
            throw Exception(
              errorResponse['error'] ??
                  errorResponse['message'] ??
                  'Error del servidor',
            );
          } catch (e) {
            throw Exception(
              'Error en el servidor (${confirmResponse.statusCode}): ${confirmResponse.body}',
            );
          }
        }

        final confirmResult = jsonDecode(confirmResponse.body);

        if (confirmResult['success'] != true) {
          debugPrint('❌ ERROR EN RESPUESTA: ${confirmResult['message']}');
          throw Exception(
            confirmResult['message'] ?? 'Error al confirmar el pago',
          );
        }

        debugPrint('✅ Pago confirmado exitosamente');
        debugPrint('📄 Folio: ${confirmResult['folio']}');

        setState(() {
          _pagoRealizado = true;
          _folioVenta = confirmResult['folio'];
          _procesandoPago = false;
        });

        // ✅ Navegar a TicketPage después de pago exitoso
        await _navegarATicketPage();
      } else if (_metodoPagoSeleccionado == 'billetera') {
        // ✅ Para billetera digital
        debugPrint('🔄 Procesando pago con billetera...');

        String datosViajeJson = _crearDatosViajeJson();

        final sale = Sale(
          userId: _userId!,
          routeUnitScheduleId: widget.routeUnitScheduleId,
          rateId: widget.rateId,
          data: datosViajeJson, // ✅ Enviar como string JSON con formato exacto
          amount: total,
          paymentId: paymentMethods[_metodoPagoSeleccionado]!,
          seats: widget.asientos, // ✅ Asientos requeridos por backend
        );

        debugPrint('📦 Sale creada: ${sale.toJson()}');

        final saleResult = await SaleApiService.createSale(sale);

        if (saleResult['success'] != true) {
          throw Exception(saleResult['message'] ?? 'Error al procesar el pago');
        }

        setState(() {
          _pagoRealizado = true;
          _folioVenta = saleResult['folio'];
          _procesandoPago = false;
        });

        // ✅ Navegar a TicketPage después de pago exitoso
        await _navegarATicketPage();
      }
    } catch (e) {
      debugPrint('💥 ERROR DETALLADO EN PAGO:');
      debugPrint('   - Tipo: ${e.runtimeType}');
      debugPrint('   - Mensaje: $e');

      if (e is http.ClientException) {
        debugPrint('   - Error de conexión HTTP');
      } else if (e is StripeException) {
        debugPrint('   - Error de Stripe: ${e.error}');
      } else if (e is TimeoutException) {
        debugPrint('   - Timeout en la solicitud');
      }

      String errorMessage = e.toString().replaceAll('Exception: ', '');

      // Mensajes más amigables para el usuario
      if (errorMessage.contains('connection') ||
          errorMessage.contains('timeout') ||
          errorMessage.contains('Timeout') ||
          errorMessage.contains('socket') ||
          e is TimeoutException) {
        errorMessage =
            'Error de conexión. Verifica tu internet e intenta nuevamente.';
      } else if (errorMessage.contains('clientSecret')) {
        errorMessage =
            'Error en la configuración del pago. Contacte al soporte.';
      } else if (errorMessage.contains('Failed host lookup')) {
        errorMessage = 'Error de conexión. Verifica tu acceso a internet.';
      }

      setState(() {
        _procesandoPago = false;
        _mensajeError = 'Error en el pago: $errorMessage';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(_pagoRealizado ? "Pago Realizado" : "Finalizar Compra"),
        centerTitle: true,
        backgroundColor: const Color(0xFFFF9800),
        foregroundColor: Colors.white,
      ),
      body: _mostrarQR
          ? _buildPantallaQR()
          : _pagoRealizado
          ? _buildPagoExitoso()
          : _buildPantallaPago(),
    );
  }

  // ✅ Nueva pantalla para mostrar QR
  Widget _buildPantallaQR() {
    return Padding(
      padding: const EdgeInsets.all(24),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Text(
            "Escanea el Código QR",
            style: TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.bold,
              color: Color(0xFFFF9800),
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 16),
          Text(
            "Monto a pagar: \$${total.toStringAsFixed(2)} MXN",
            style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w600),
          ),
          const SizedBox(height: 24),
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: Colors.grey.withOpacity(0.3),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: QrImageView(
              data: _qrData!,
              version: QrVersions.auto,
              size: 250.0,
              backgroundColor: Colors.white,
            ),
          ),
          const SizedBox(height: 24),
          const Text(
            "Usa tu app de billetera digital para escanear el código",
            style: TextStyle(fontSize: 16, color: Colors.grey),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 32),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: _simularPagoQRExitoso,
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF4CAF50),
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
              child: const Text(
                "Simular Pago Exitoso",
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
              ),
            ),
          ),
          const SizedBox(height: 16),
          SizedBox(
            width: double.infinity,
            child: TextButton(
              onPressed: () {
                setState(() {
                  _mostrarQR = false;
                });
              },
              child: const Text(
                "Cancelar",
                style: TextStyle(fontSize: 16, color: Colors.grey),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPantallaPago() {
    return Stack(
      children: [
        SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildResumenCompra(),
              const SizedBox(height: 24),
              _buildMetodosPago(),
              const SizedBox(height: 24),
              if (_mensajeError != null) _buildMensajeError(),
              const SizedBox(height: 16),
              _buildBotonConfirmacion(),
              const SizedBox(height: 20),
            ],
          ),
        ),
        if (_procesandoPago) _buildOverlayCarga(),
      ],
    );
  }

  Widget _buildResumenCompra() {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  "Resumen de compra",
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 6,
                  ),
                  decoration: BoxDecoration(
                    color: const Color(0xFFFF9800).withOpacity(0.1),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    "${widget.asientos.length} ${widget.asientos.length == 1 ? 'asiento' : 'asientos'}",
                    style: const TextStyle(
                      color: Color(0xFFFF9800),
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            _buildDetalleItem("Origen:", widget.origin),
            _buildDetalleItem("Destino:", widget.destination),
            _buildDetalleItem("Asientos:", widget.asientos.join(", ")),
            _buildDetalleItem(
              "Precio unitario:",
              "\$${widget.precioPorAsiento.toStringAsFixed(2)}",
            ),
            const Divider(height: 24, thickness: 1),
            _buildTotalPagar(),
          ],
        ),
      ),
    );
  }

  Widget _buildDetalleItem(String titulo, String valor) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            titulo,
            style: TextStyle(
              color: Colors.grey.shade700,
              fontSize: 16,
              fontWeight: FontWeight.w500,
            ),
          ),
          Text(
            valor,
            style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 16),
          ),
        ],
      ),
    );
  }

  Widget _buildTotalPagar() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        const Text(
          "Total a pagar:",
          style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
        ),
        Text(
          "\$${total.toStringAsFixed(2)} MXN",
          style: const TextStyle(
            fontSize: 22,
            fontWeight: FontWeight.bold,
            color: Color(0xFFFF9800),
          ),
        ),
      ],
    );
  }

  Widget _buildMetodosPago() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(
          padding: EdgeInsets.only(left: 4, bottom: 16),
          child: Text(
            "Selecciona método de pago",
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
          ),
        ),
        _buildTarjetaPago(
          icon: Icons.credit_card,
          color: Colors.blue,
          titulo: "Tarjeta de crédito/débito",
          subtitulo: "Paga con Visa, Mastercard o American Express",
          metodo: "tarjeta",
        ),
        const SizedBox(height: 12),
        _buildTarjetaPago(
          icon: Icons.qr_code_2,
          color: Colors.green,
          titulo: "Pago con QR",
          subtitulo: "Escanea el código QR para pagar",
          metodo: "qr",
        ),
        const SizedBox(height: 12),
        _buildTarjetaPago(
          icon: Icons.account_balance_wallet,
          color: Colors.purple,
          titulo: "Pago en efectivo ",
          subtitulo: "Pago con efectivo o venta en taquilla",
          metodo: "billetera",
        ),
      ],
    );
  }

  Widget _buildTarjetaPago({
    required IconData icon,
    required Color color,
    required String titulo,
    required String subtitulo,
    required String metodo,
  }) {
    final bool seleccionado = _metodoPagoSeleccionado == metodo;

    return InkWell(
      borderRadius: BorderRadius.circular(12),
      onTap: () => _seleccionarMetodoPago(metodo),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: seleccionado
                ? const Color(0xFFFF9800)
                : Colors.grey.shade300,
            width: seleccionado ? 2 : 1,
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.grey.withOpacity(0.1),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 28),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    titulo,
                    style: const TextStyle(
                      fontWeight: FontWeight.w600,
                      fontSize: 16,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    subtitulo,
                    style: TextStyle(color: Colors.grey.shade600, fontSize: 14),
                  ),
                ],
              ),
            ),
            Container(
              width: 24,
              height: 24,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(
                  color: seleccionado
                      ? const Color(0xFFFF9800)
                      : Colors.grey.shade400,
                  width: 2,
                ),
                color: seleccionado
                    ? const Color(0xFFFF9800)
                    : Colors.transparent,
              ),
              child: seleccionado
                  ? const Icon(Icons.check, size: 16, color: Colors.white)
                  : null,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBotonConfirmacion() {
    return SizedBox(
      width: double.infinity,
      child: ElevatedButton(
        onPressed: _procesandoPago ? null : _realizarPago,
        style: ElevatedButton.styleFrom(
          backgroundColor: const Color(0xFFFF9800),
          foregroundColor: Colors.white,
          padding: const EdgeInsets.symmetric(vertical: 18),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          elevation: 2,
        ),
        child: _procesandoPago
            ? const SizedBox(
                width: 24,
                height: 24,
                child: CircularProgressIndicator(
                  strokeWidth: 3,
                  color: Colors.white,
                ),
              )
            : const Text(
                "Confirmar y Pagar",
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
      ),
    );
  }

  Widget _buildMensajeError() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.red.shade50,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.red.shade200),
      ),
      child: Row(
        children: [
          Icon(Icons.error_outline, color: Colors.red.shade600),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              _mensajeError!,
              style: TextStyle(
                color: Colors.red.shade800,
                fontSize: 14,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildOverlayCarga() {
    return Container(
      color: Colors.black.withOpacity(0.5),
      child: const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            CircularProgressIndicator(
              valueColor: AlwaysStoppedAnimation<Color>(Color(0xFFFF9800)),
              strokeWidth: 4,
            ),
            SizedBox(height: 16),
            Text(
              "Procesando pago...",
              style: TextStyle(
                color: Colors.white,
                fontSize: 16,
                fontWeight: FontWeight.w500,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPagoExitoso() {
    return Container(
      padding: const EdgeInsets.all(24),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: 100,
            height: 100,
            decoration: const BoxDecoration(
              color: Color(0xFF4CAF50),
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.check, size: 60, color: Colors.white),
          ),
          const SizedBox(height: 24),
          const Text(
            "¡Pago Exitoso!",
            style: TextStyle(
              fontSize: 28,
              fontWeight: FontWeight.bold,
              color: Color(0xFF4CAF50),
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 16),
          Text(
            "Método de pago: ${_metodoPagoSeleccionado?.toUpperCase()}",
            style: const TextStyle(fontSize: 16, color: Colors.grey),
          ),
          if (_folioVenta != null) ...[
            const SizedBox(height: 12),
            Text(
              "Folio: $_folioVenta",
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w600,
                color: Colors.black87,
              ),
            ),
          ],
          const SizedBox(height: 8),
          Text(
            "Monto: \$${total.toStringAsFixed(2)} MXN",
            style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w600),
          ),
          const SizedBox(height: 32),
          const CircularProgressIndicator(color: Color(0xFFFF9800)),
          const SizedBox(height: 16),
          const Text(
            "Redirigiendo al ticket...",
            style: TextStyle(color: Colors.grey, fontSize: 14),
          ),
        ],
      ),
    );
  }
}
