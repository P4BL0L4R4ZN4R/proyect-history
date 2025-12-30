import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:rutvans_mobile/src/services/travel_services/departure_service.dart';
import 'package:rutvans_mobile/src/services/travel_services/transport_service.dart';
import 'package:rutvans_mobile/src/screens/travel/ticket_selection.dart';

const Color primaryOrange = Color(0xFFFF9800);
const Color primaryOrangeDark = Color(0xFFFF9800);
const Color textGray = Color(0xFF454545);

class DeparturePage extends StatefulWidget {
  final String? origin;
  final String? destination;

  const DeparturePage({super.key, this.origin, this.destination});

  @override
  State<DeparturePage> createState() => _DeparturePageState();
}

class _DeparturePageState extends State<DeparturePage>
    with SingleTickerProviderStateMixin {
  final DepartureService _departureService = DepartureService();
  List<Map<String, dynamic>> _schedules = [];
  List<Map<String, dynamic>> _availableSchedules = [];
  bool _isLoading = true;
  bool _error = false;
  bool _showTimeNotification = false;
  String _errorMessage = '';
  late AnimationController _animationController;
  late Animation<Offset> _slideAnimation;

  @override
  void initState() {
    super.initState();
    
    // 🔍 Logs de datos iniciales recibidos por la página
    debugPrint('---[DEBUG DeparturePage] Datos recibidos al crear la página---');
    debugPrint('origin: ${widget.origin}');
    debugPrint('destination: ${widget.destination}');
    debugPrint('-----------------------------------------------');

    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 600),
    );
    _slideAnimation =
        Tween<Offset>(begin: const Offset(0.0, -1.5), end: Offset.zero).animate(
          CurvedAnimation(
            parent: _animationController,
            curve: Curves.elasticOut,
          ),
        );
    _fetchSchedules();
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  void _ocultarNotificacion() {
    if (!mounted) return;

    _animationController.reverse().then((_) {
      if (mounted) {
        setState(() {
          _showTimeNotification = false;
        });
      }
    });
  }

  Future<void> _fetchSchedules() async {
    try {
      setState(() {
        _isLoading = true;
        _error = false;
        _errorMessage = '';
      });

      final schedules = await _departureService.getRouteUnitSchedules(
        origin: widget.origin,
        destination: widget.destination,
      );

      debugPrint('✅ [DepartureService] Obtenidos ${schedules.length} horarios: $schedules');

      if (mounted) {
        final now = DateTime.now();
        final availableSchedules = schedules.where((schedule) {
          final time = schedule['schedule_time']?.toString() ?? '00:00:00';
          final scheduleDate =
              schedule['schedule_date']?.toString() ??
              DateFormat('yyyy-MM-dd').format(now);
          try {
            final departureDate = DateTime.parse('$scheduleDate $time');
            return departureDate.isAfter(now);
          } catch (_) {
            return false;
          }
        }).toList();

        final hasExpiredSchedules =
            availableSchedules.length < schedules.length;

        setState(() {
          _schedules = schedules;
          _availableSchedules = availableSchedules;
          _isLoading = false;
          _error = false;
          _showTimeNotification = hasExpiredSchedules;
        });

        if (hasExpiredSchedules) {
          _animationController.forward();
          Future.delayed(const Duration(seconds: 3), () {
            if (mounted && _showTimeNotification) {
              _ocultarNotificacion();
            }
          });
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _error = true;
          _isLoading = false;
          _errorMessage = _getErrorMessage(e);
        });
      }
      debugPrint('[ERROR] Error fetching schedules: $e');
    }
  }

  String _getErrorMessage(dynamic error) {
    if (error.toString().contains('Connection') ||
        error.toString().contains('Socket') ||
        error.toString().contains('Network')) {
      return 'Problema de conexión. Verifica tu internet e intenta nuevamente.';
    } else if (error.toString().contains('Timeout')) {
      return 'La solicitud está tardando demasiado. Intenta nuevamente.';
    } else if (error.toString().contains('404') ||
        error.toString().contains('No route found')) {
      return 'No se encontraron rutas para esta combinación.';
    } else {
      return 'Ocurrió un error inesperado. Por favor, intenta más tarde.';
    }
  }

  // Widget para la notificación de horarios expirados
  Widget _buildTimeNotification() {
    if (!_showTimeNotification) return const SizedBox.shrink();

    final expiredCount = _schedules.length - _availableSchedules.length;

    return SlideTransition(
      position: _slideAnimation,
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [Colors.orange.shade50, Colors.orange.shade100],
          ),
          border: Border(
            bottom: BorderSide(color: Colors.orange.shade300, width: 1.5),
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.orange.shade200.withOpacity(0.3),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: Colors.orange.shade100,
                shape: BoxShape.circle,
              ),
              child: Icon(
                Icons.access_time_rounded,
                color: Colors.orange.shade700,
                size: 18,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Horarios no disponibles',
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      color: Colors.orange.shade800,
                      fontSize: 14,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    '$expiredCount horario(s) han pasado y no están disponibles.',
                    style: TextStyle(
                      color: Colors.orange.shade700,
                      fontSize: 12,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
            ),
            const SizedBox(width: 8),
            GestureDetector(
              onTap: _ocultarNotificacion,
              child: Container(
                padding: const EdgeInsets.all(4),
                decoration: BoxDecoration(
                  color: Colors.orange.shade200,
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  Icons.close_rounded,
                  color: Colors.orange.shade700,
                  size: 16,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Método para verificar si un horario ya pasó
  bool _isScheduleExpired(Map<String, dynamic> schedule) {
    final time = schedule['schedule_time']?.toString() ?? '00:00:00';
    final scheduleDate =
        schedule['schedule_date']?.toString() ??
        DateFormat('yyyy-MM-dd').format(DateTime.now());
    try {
      final departureDate = DateTime.parse('$scheduleDate $time');
      return departureDate.isBefore(DateTime.now());
    } catch (_) {
      return true;
    }
  }

  void _navigateToSeleccionBoletos(Map<String, dynamic> schedule) {
    if (_isScheduleExpired(schedule)) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text(
            'Este horario ya no está disponible. Por favor selecciona otro horario.',
          ),
          backgroundColor: Colors.orange,
          duration: const Duration(seconds: 3),
        ),
      );
      return;
    }

    debugPrint('[DEPARTURE] schedule recibido: $schedule');

    int unitId = 0;
    if (schedule['unit_id'] != null) {
      unitId = (schedule['unit_id'] is int)
          ? schedule['unit_id']
          : int.tryParse(schedule['unit_id'].toString()) ?? 0;
    }

    final routeUnitScheduleId = schedule['id'] is int
        ? schedule['id']
        : int.tryParse(schedule['id']?.toString() ?? '') ?? 0;

    final rateId = schedule['rate_id'] is int
        ? schedule['rate_id']
        : int.tryParse(schedule['rate_id']?.toString() ?? '') ?? 1;

    int? unitCapacity;
    final rawCapacity = schedule['capacity'];
    if (rawCapacity != null) {
      unitCapacity = (rawCapacity is int)
          ? rawCapacity
          : int.tryParse(rawCapacity?.toString() ?? '');
    }

    debugPrint('[DEPARTURE] unitId: $unitId, unitCapacity: $unitCapacity');

    if (unitId == 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('No se pudo obtener el ID de la unidad. Contacta a soporte.'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (unitCapacity == null || unitCapacity <= 0) {
      TransportApiService.getUnitInfo(unitId.toString()).then((unitInfo) {
        final apiCapacity = (unitInfo != null && unitInfo['capacity'] != null)
            ? int.tryParse(unitInfo['capacity'].toString())
            : null;
        if (apiCapacity != null && apiCapacity > 0) {
          _pushSeleccionBoletos(schedule, unitId, routeUnitScheduleId, rateId, apiCapacity);
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('No se pudo obtener la información de la unidad o la capacidad es inválida'),
              backgroundColor: Colors.red,
            ),
          );
        }
      });
      return;
    }

    _pushSeleccionBoletos(schedule, unitId, routeUnitScheduleId, rateId, unitCapacity);
  }

  void _pushSeleccionBoletos(
    Map<String, dynamic> schedule,
    int unitId,
    int routeUnitScheduleId,
    int rateId,
    int unitCapacity,
  ) {
    // 🔹 Origen y destino - USANDO LA LÓGICA ORIGINAL
    final description = schedule['route_unit_description']?.toString() ?? '';
    final parts = description.split('-');
    final origin = schedule['origin']?.toString() ?? 'No disponible';
    final destination = schedule['destination']?.toString() ?? 'No disponible';

    // 🔹 Fecha y hora combinadas
    final scheduleDate = schedule['schedule_date']?.toString() ?? '';
    final scheduleTime = schedule['schedule_time']?.toString() ?? '00:00:00';

    DateTime travelDate;
    try {
      travelDate = DateTime.parse('$scheduleDate $scheduleTime');
    } catch (e) {
      debugPrint('[ERROR] No se pudo parsear fecha/hora: $e');
      travelDate = DateTime.now();
    }

    // 🔹 Duración
    final durationSeconds = (schedule['estimated_duration_seconds'] as num?)?.toInt() ?? 3600;
    final duration = Duration(seconds: durationSeconds);

    // 🔹 Logs completos
    debugPrint('---[DEBUG pushSeleccionBoletos] Datos que se envían---');
    debugPrint('unitId: $unitId');
    debugPrint('routeUnitScheduleId: $routeUnitScheduleId');
    debugPrint('rateId: $rateId');
    debugPrint('unitCapacity: $unitCapacity');
    debugPrint('travelDate: $travelDate');
    debugPrint('origin: $origin');
    debugPrint('destination: $destination');
    debugPrint('duration: $duration');
    debugPrint('driverName: ${schedule['driver_name']?.toString() ?? 'No disponible'}');
    debugPrint('--------------------------------------------------------');

    Navigator.push(
      context,
      PageRouteBuilder(
        transitionDuration: const Duration(milliseconds: 500),
        pageBuilder: (_, __, ___) => SeleccionBoletosScreen(
          unitId: unitId,
          routeUnitScheduleId: routeUnitScheduleId,
          rateId: rateId,
          unitCapacity: unitCapacity,
          travelDate: travelDate,
          origin: origin,
          destination: destination,
          duration: duration,
          driverName: schedule['driver_name']?.toString() ?? 'No disponible',
        ),
        transitionsBuilder: (_, anim, __, child) {
          return FadeTransition(opacity: anim, child: child);
        },
      ),
    );
  }

  Widget _buildErrorWidget() {
    return Center(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Ilustración de error
            Container(
              height: 200,
              margin: const EdgeInsets.only(bottom: 32),
              child: Stack(
                alignment: Alignment.center,
                children: [
                  // Fondo decorativo
                  Container(
                    width: 160,
                    height: 160,
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [Colors.red.shade50, Colors.red.shade100],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      shape: BoxShape.circle,
                    ),
                  ),
                  // Ícono principal
                  Positioned(
                    child: Container(
                      padding: const EdgeInsets.all(24),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                        boxShadow: [
                          BoxShadow(
                            color: Colors.red.withOpacity(0.2),
                            blurRadius: 20,
                            offset: const Offset(0, 10),
                          ),
                        ],
                      ),
                      child: Icon(
                        Icons.error_outline_rounded,
                        size: 64,
                        color: Colors.red.shade600,
                      ),
                    ),
                  ),
                  // Elementos decorativos
                  Positioned(
                    top: 40,
                    right: 60,
                    child: Icon(
                      Icons.wifi_off_rounded,
                      size: 24,
                      color: Colors.red.shade400,
                    ),
                  ),
                  Positioned(
                    bottom: 50,
                    left: 60,
                    child: Icon(
                      Icons.sync_problem_rounded,
                      size: 20,
                      color: Colors.red.shade400,
                    ),
                  ),
                ],
              ),
            ),

            // Texto de error
            Text(
              _errorMessage.isEmpty ? 'Error de conexión' : _errorMessage,
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.w700,
                color: Colors.black87,
                height: 1.4,
              ),
            ),

            const SizedBox(height: 16),

            const Text(
              'No pudimos cargar los horarios en este momento',
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey, fontSize: 16, height: 1.5),
            ),

            const SizedBox(height: 40),

            // Botón de acción
            Container(
              width: double.infinity,
              height: 56,
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [primaryOrange, primaryOrangeDark],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: primaryOrange.withOpacity(0.3),
                    blurRadius: 15,
                    offset: const Offset(0, 8),
                  ),
                ],
              ),
              child: Material(
                color: Colors.transparent,
                child: InkWell(
                  borderRadius: BorderRadius.circular(16),
                  onTap: _fetchSchedules,
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(
                        Icons.refresh_rounded,
                        color: Colors.white,
                        size: 24,
                      ),
                      const SizedBox(width: 12),
                      const Text(
                        'REINTENTAR',
                        style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.w800,
                          fontSize: 16,
                          letterSpacing: 0.5,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),

            const SizedBox(height: 20),

            // Botón secundario
            TextButton(
              onPressed: () {
                Navigator.of(context).pop();
              },
              child: const Text(
                'Volver atrás',
                style: TextStyle(
                  color: Colors.grey,
                  fontWeight: FontWeight.w600,
                  fontSize: 15,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildNoSchedulesWidget() {
    return Center(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Título
            const Text(
              'No hay horarios disponibles',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.w800,
                color: Colors.black87,
                height: 1.3,
              ),
            ),

            const SizedBox(height: 12),

            // Ruta
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
              decoration: BoxDecoration(
                color: primaryOrange.withOpacity(0.1),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: primaryOrange.withOpacity(0.2)),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    widget.origin ?? 'Origen',
                    style: const TextStyle(
                      color: primaryOrange,
                      fontWeight: FontWeight.w700,
                      fontSize: 16,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Icon(
                    Icons.arrow_forward_rounded,
                    color: primaryOrange,
                    size: 18,
                  ),
                  const SizedBox(width: 12),
                  Text(
                    widget.destination ?? 'Destino',
                    style: const TextStyle(
                      color: primaryOrange,
                      fontWeight: FontWeight.w700,
                      fontSize: 16,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 24),

            // Mensaje descriptivo
            const Text(
              'Actualmente no tenemos horarios programados\npara esta ruta en específico.',
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey, fontSize: 16, height: 1.6),
            ),

            const SizedBox(height: 8),

            const Text(
              'Puedes intentar con otras rutas o verificar más tarde.',
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey, fontSize: 14, height: 1.5),
            ),

            const SizedBox(height: 40),

            // Botón principal
            Container(
              width: double.infinity,
              height: 56,
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [primaryOrange, primaryOrangeDark],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: primaryOrange.withOpacity(0.3),
                    blurRadius: 15,
                    offset: const Offset(0, 8),
                  ),
                ],
              ),
              child: Material(
                color: Colors.transparent,
                child: InkWell(
                  borderRadius: BorderRadius.circular(16),
                  onTap: _fetchSchedules,
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.search_rounded, color: Colors.white, size: 24),
                      const SizedBox(width: 12),
                      const Text(
                        'BUSCAR DE NUEVO',
                        style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.w800,
                          fontSize: 16,
                          letterSpacing: 0.5,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),

            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFD),
      body: SafeArea(
        child: Column(
          children: [
            _buildHeader(context),
            // Notificación de horarios expirados
            _buildTimeNotification(),
            Expanded(
              child: _isLoading
                  ? _buildLoadingWidget()
                  : _error
                  ? _buildErrorWidget()
                  : _availableSchedules.isEmpty
                  ? _buildNoSchedulesWidget()
                  : RefreshIndicator(
                      color: primaryOrange,
                      backgroundColor: Colors.white,
                      strokeWidth: 3.0,
                      onRefresh: _fetchSchedules,
                      child: ListView.builder(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 16,
                          vertical: 12,
                        ),
                        itemCount: _availableSchedules.length,
                        itemBuilder: (context, index) {
                          final schedule = _availableSchedules[index];
                          return _buildScheduleCard(schedule);
                        },
                      ),
                    ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLoadingWidget() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          // Animación de carga personalizada
          Container(
            width: 80,
            height: 80,
            margin: const EdgeInsets.only(bottom: 24),
            decoration: BoxDecoration(
              color: Colors.white,
              shape: BoxShape.circle,
              boxShadow: [
                BoxShadow(
                  color: primaryOrange.withOpacity(0.2),
                  blurRadius: 15,
                  offset: const Offset(0, 8),
                ),
              ],
            ),
            child: Stack(
              children: [
                Center(
                  child: SizedBox(
                    width: 40,
                    height: 40,
                    child: CircularProgressIndicator(
                      strokeWidth: 3,
                      valueColor: AlwaysStoppedAnimation<Color>(primaryOrange),
                    ),
                  ),
                ),
                Center(
                  child: Icon(
                    Icons.directions_bus_rounded,
                    color: primaryOrange,
                    size: 24,
                  ),
                ),
              ],
            ),
          ),
          const Text(
            'Buscando horarios...',
            style: TextStyle(
              color: Colors.grey,
              fontSize: 16,
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 8),
          const Text(
            'Estamos consultando las rutas disponibles',
            style: TextStyle(color: Colors.grey, fontSize: 14),
          ),
        ],
      ),
    );
  }

  Widget _buildHeader(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 18),
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          colors: [primaryOrange, primaryOrangeDark],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(20),
          bottomRight: Radius.circular(20),
        ),
        boxShadow: [
          BoxShadow(color: Colors.black26, blurRadius: 8, offset: Offset(0, 3)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SizedBox(height: 14),
          const Text(
            'Horarios Disponibles',
            style: TextStyle(
              color: Colors.white,
              fontSize: 26,
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            'De ${widget.origin ?? 'Origen'} a ${widget.destination ?? 'Destino'}',
            style: const TextStyle(color: Colors.white70, fontSize: 13),
          ),
        ],
      ),
    );
  }

  Widget _buildScheduleCard(Map<String, dynamic> schedule) {
    final time = schedule['schedule_time']?.toString() ?? '00:00:00';
    final scheduleDate =
        schedule['schedule_date']?.toString() ??
        DateFormat('yyyy-MM-dd').format(DateTime.now());
    DateTime departureDate;
    try {
      departureDate = DateTime.parse('$scheduleDate $time');
    } catch (_) {
      departureDate = DateTime.now();
    }
    final formattedDepartureTime = DateFormat('h:mm a').format(departureDate);

    // 🔹 USANDO LA LÓGICA ORIGINAL PARA ORIGEN Y DESTINO
    final description = schedule['route_unit_description']?.toString() ?? '';
    final parts = description.split('-');
    final origin = schedule['origin']?.toString() ?? 'No disponible';
    final destination = schedule['destination']?.toString() ?? 'No disponible';

    final imageUrl = schedule['font_url']?.toString();

    return Container(
      margin: const EdgeInsets.only(bottom: 18),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(16),
          onTap: () {
            setState(() {
              schedule['isExpanded'] = !(schedule['isExpanded'] ?? false);
            });
          },
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 280),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: Colors.black12,
                  blurRadius: 10,
                  offset: const Offset(0, 6),
                ),
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                ClipRRect(
                  borderRadius: const BorderRadius.vertical(
                    top: Radius.circular(16),
                  ),
                  child: Stack(
                    children: [
                      Hero(
                        tag: 'schedule_image_${schedule['id'] ?? UniqueKey()}',
                        child: SizedBox(
                          height: 150,
                          width: double.infinity,
                          child: imageUrl != null && imageUrl.isNotEmpty
                              ? Image.network(
                                  imageUrl,
                                  fit: BoxFit.cover,
                                  errorBuilder: (_, __, ___) {
                                    return Image.asset(
                                      'assets/images/vanImage.jpg',
                                      fit: BoxFit.cover,
                                    );
                                  },
                                )
                              : Image.asset(
                                  'assets/images/vanImage.jpg',
                                  fit: BoxFit.cover,
                                ),
                        ),
                      ),
                      Container(
                        height: 150,
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            colors: [
                              Colors.transparent,
                              Colors.black.withOpacity(0.55),
                            ],
                            begin: Alignment.topCenter,
                            end: Alignment.bottomCenter,
                          ),
                        ),
                      ),
                      Positioned(
                        left: 14,
                        bottom: 12,
                        child: Row(
                          children: [
                            Text(
                              origin,
                              style: const TextStyle(
                                color: Colors.white,
                                fontWeight: FontWeight.w700,
                                fontSize: 16,
                              ),
                            ),
                            const SizedBox(width: 6),
                            const Icon(
                              Icons.swap_horiz,
                              color: Colors.white,
                              size: 18,
                            ),
                            const SizedBox(width: 6),
                            Text(
                              destination,
                              style: const TextStyle(
                                color: Colors.white,
                                fontWeight: FontWeight.w700,
                                fontSize: 16,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Positioned(
                        right: 14,
                        bottom: 12,
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 10,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.black.withOpacity(0.3),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            formattedDepartureTime,
                            style: const TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.w600,
                              fontSize: 14,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                AnimatedCrossFade(
                  firstChild: _buildCompactDetails(schedule),
                  secondChild: _buildGlassExpandedDetails(schedule),
                  crossFadeState: schedule['isExpanded'] ?? false
                      ? CrossFadeState.showSecond
                      : CrossFadeState.showFirst,
                  duration: const Duration(milliseconds: 260),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildCompactDetails(Map<String, dynamic> schedule) {
    final modelo = schedule['unit_model']?.toString() ?? 'Modelo N/D';
    final capacidad = schedule['capacity']?.toString() ?? 'N/D';

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  modelo,
                  style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 6),
                Row(
                  children: [
                    Icon(Icons.event_seat, size: 16, color: primaryOrange),
                    const SizedBox(width: 6),
                    Text(
                      'Capacidad: $capacidad',
                      style: const TextStyle(fontSize: 13),
                    ),
                  ],
                ),
              ],
            ),
          ),
          GestureDetector(
            onTap: () => _navigateToSeleccionBoletos(schedule),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [primaryOrange, primaryOrangeDark],
                ),
                borderRadius: BorderRadius.circular(10),
                boxShadow: [
                  BoxShadow(
                    color: primaryOrange.withOpacity(0.25),
                    blurRadius: 8,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Row(
                children: const [
                  Icon(
                    Icons.airline_seat_recline_normal_rounded,
                    color: Colors.white,
                    size: 18,
                  ),
                  SizedBox(width: 8),
                  Text(
                    'Seleccionar',
                    style: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildGlassExpandedDetails(Map<String, dynamic> schedule) {
    return ClipRRect(
      borderRadius: const BorderRadius.vertical(bottom: Radius.circular(16)),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 8, sigmaY: 8),
        child: Container(
          color: Colors.white.withOpacity(0.85),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          child: Column(
            children: [
              Row(
                children: [
                  _smallInfoTile(
                    Icons.person,
                    'Conductor',
                    schedule['driver_name']?.toString() ?? 'No disponible',
                  ),
                  const SizedBox(width: 12),
                  _smallInfoTile(
                    Icons.directions_car,
                    'Modelo',
                    schedule['unit_model']?.toString() ?? 'N/D',
                  ),
                ],
              ),
              const SizedBox(height: 10),
              Row(
                children: [
                  _smallInfoTile(
                    Icons.event_seat,
                    'Capacidad',
                    schedule['capacity']?.toString() ?? 'N/D',
                  ),
                  const SizedBox(width: 12),
                  _smallInfoTile(
                    Icons.confirmation_number,
                    'Placas',
                    schedule['license_plate']?.toString() ?? 'N/D',
                  ),
                ],
              ),
              const SizedBox(height: 14),
              Row(
                children: [
                  Expanded(
                    child: ElevatedButton.icon(
                      onPressed: () => _navigateToSeleccionBoletos(schedule),
                      icon: const Icon(
                        Icons.airline_seat_recline_normal_rounded,
                        size: 18,
                        color: Colors.white,
                      ),
                      label: const Padding(
                        padding: EdgeInsets.symmetric(vertical: 12),
                        child: Text(
                          'Seleccionar boletos',
                          style: TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.w700,
                            color: Colors.white,
                          ),
                        ),
                      ),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: primaryOrange,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              TextButton(
                onPressed: () {
                  setState(() {
                    schedule['isExpanded'] = false;
                  });
                },
                child: const Text(
                  'Ocultar detalles',
                  style: TextStyle(color: Color.fromARGB(255, 0, 0, 0)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _smallInfoTile(IconData icon, String title, String value) {
    return Expanded(
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: primaryOrange.withOpacity(0.12),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(icon, size: 18, color: primaryOrange),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(fontSize: 12, color: Colors.black54),
                ),
                const SizedBox(height: 2),
                Text(
                  value,
                  style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}