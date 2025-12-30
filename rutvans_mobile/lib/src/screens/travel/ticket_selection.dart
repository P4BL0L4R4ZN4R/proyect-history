import 'package:flutter/material.dart';
import 'seat_selection.dart';
import 'package:rutvans_mobile/src/services/travel_services/transport_service.dart';
import 'package:rutvans_mobile/src/services/travel_services/ticket_selection_service.dart';

const Color primaryOrange = Color(0xFFFF9800);
const Color darkOrange = Color(0xFFE65100);
const Color lightOrange = Color(0xFFFFE0B2);
const Color backgroundColor = Color(0xFFF5F5F5);
const Color cardColor = Color(0xFFFFFFFF);
const Color textSecondary = Color(0xFF666666);

// Tarifas por tipo
const int tarifaPasajero = 40;
const int tarifaEstudiante = 30;
const int tarifaAdultoMayor = 25;

class SeleccionBoletosScreen extends StatefulWidget {
  final int unitId;
  final int routeUnitScheduleId;
  final int rateId;
  final int unitCapacity;
  final DateTime travelDate;
  final Duration duration;
  final String origin;
  final String destination;
  final String driverName;

  const SeleccionBoletosScreen({
    super.key,
    required this.unitId,
    required this.routeUnitScheduleId,
    required this.rateId,
    required this.unitCapacity,
    required this.travelDate,
    required this.duration,
    required this.origin,
    required this.destination,
    required this.driverName,
  });

  @override
  State<SeleccionBoletosScreen> createState() => _SeleccionBoletosScreenState();
}

class _SeleccionBoletosScreenState extends State<SeleccionBoletosScreen>
    with SingleTickerProviderStateMixin {
  int estudiantes = 0;
  int pasajeros = 0; // Cambiado a 0 para ser fiel al diseño
  int adultosMayores = 0;
  int? _unitCapacity;
  int _occupiedSeats = 0;
  bool _isLoading = true;
  late AnimationController _animationController;
  late Animation<double> _scaleAnimation;

  @override
  void initState() {
    super.initState();

    // Log de depuración para verificar que los datos llegaron correctamente
    debugPrint('[DEBUG SeleccionBoletosScreen] unitId: ${widget.unitId}');
    debugPrint(
      '[DEBUG SeleccionBoletosScreen] routeUnitScheduleId: ${widget.routeUnitScheduleId}',
    );
    debugPrint('[DEBUG SeleccionBoletosScreen] rateId: ${widget.rateId}');
    debugPrint(
      '[DEBUG SeleccionBoletosScreen] unitCapacity: ${widget.unitCapacity}',
    );
    debugPrint(
      '[DEBUG SeleccionBoletosScreen] travelDate: ${widget.travelDate}',
    );
    debugPrint(
      '[DEBUG SeleccionBoletosScreen] duration: ${widget.duration.inSeconds} segundos',
    );
    debugPrint('[DEBUG SeleccionBoletosScreen] origin: ${widget.origin}');
    debugPrint(
      '[DEBUG SeleccionBoletosScreen] destination: ${widget.destination}',
    );
    debugPrint(
      '[DEBUG SeleccionBoletosScreen] driverName: ${widget.driverName}',
    );

    _unitCapacity = widget.unitCapacity;
    
    // Configuración de animaciones del diseño
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 600),
    );
    _scaleAnimation = Tween<double>(begin: 0.9, end: 1.0).animate(
      CurvedAnimation(parent: _animationController, curve: Curves.easeOutBack),
    );
    _animationController.forward();
    
    _loadUnitData();
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  Future<void> _loadUnitData() async {
    try {
      debugPrint('🔍 [_loadUnitData] Iniciando carga de datos...');
      debugPrint('🔍 [_loadUnitData] unitId: ${widget.unitId}');
      debugPrint(
        '🔍 [_loadUnitData] routeUnitScheduleId: ${widget.routeUnitScheduleId}',
      );

      final unitInfo = await TransportApiService.getUnitInfo(
        widget.unitId.toString(),
      );
      debugPrint('🔍 [_loadUnitData] unitInfo obtenida: $unitInfo');

      final seatsInfo = await TicketSelectionService.getOccupiedSeats(
        widget.unitId.toString(),
        widget.routeUnitScheduleId,
      );
      debugPrint('🔍 [_loadUnitData] seatsInfo obtenida: $seatsInfo');
      final capacity = (unitInfo != null && unitInfo['capacity'] != null)
          ? unitInfo['capacity']
          : widget.unitCapacity;
      final occupiedCount = seatsInfo.length;

      debugPrint('🔍 [_loadUnitData] Capacidad calculada: $capacity');
      debugPrint(
        '🔍 [_loadUnitData] Asientos ocupados calculados: $occupiedCount',
      );
      debugPrint(
        '🔍 [_loadUnitData] widget.unitCapacity: ${widget.unitCapacity}',
      );

      setState(() {
        _unitCapacity = capacity;
        _occupiedSeats = occupiedCount;
        _isLoading = false;
      });

      debugPrint(
        '🔍 [_loadUnitData] Estado actualizado - _unitCapacity: $_unitCapacity, _occupiedSeats: $_occupiedSeats',
      );
    } catch (e) {
      debugPrint('❌ [_loadUnitData] Error cargando datos: $e');
      debugPrint('❌ [_loadUnitData] Stack trace: ${StackTrace.current}');
      setState(() {
        _unitCapacity = widget.unitCapacity;
        _occupiedSeats = 0;
        _isLoading = false;
      });
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            'No se pudo obtener la información actualizada de la unidad. Se usará la capacidad predeterminada.',
          ),
          backgroundColor: Colors.orange,
        ),
      );
    }
  }

  int get maxBoletosDisponibles {
    if (_unitCapacity == null) {
      debugPrint(
        '🔍 [maxBoletosDisponibles] _unitCapacity es null, retornando 10',
      );
      return 10;
    }
    final disponibles = _unitCapacity! - _occupiedSeats;
    debugPrint(
      '🔍 [maxBoletosDisponibles] Capacidad: $_unitCapacity, Ocupados: $_occupiedSeats, Disponibles: $disponibles',
    );
    return disponibles;
  }

  void _continuarAAsientos() {
    final cantidadTotal = estudiantes + pasajeros + adultosMayores;

    if (cantidadTotal == 0) {
      _showErrorDialog(
        '¡Ups!',
        'Debes seleccionar al menos un boleto para continuar.',
      );
      return;
    }

    if (cantidadTotal > maxBoletosDisponibles) {
      _showErrorDialog(
        'Capacidad máxima',
        'No hay suficientes asientos disponibles.\n\nMáximo disponible: $maxBoletosDisponibles',
      );
      return;
    }

    final totalAPagar =
        (estudiantes * tarifaEstudiante) +
        (pasajeros * tarifaPasajero) +
        (adultosMayores * tarifaAdultoMayor);

    final detalle = <String, int>{
      'estudiantes': estudiantes,
      'pasajeros': pasajeros,
      'adultosMayores': adultosMayores,
      'total': totalAPagar,
    };

    print('[DEBUG ticket_selection.dart] unitId: ${widget.unitId}');
    print(
      '[DEBUG ticket_selection.dart] routeUnitScheduleId: ${widget.routeUnitScheduleId}',
    );
    print('[DEBUG ticket_selection.dart] rateId: ${widget.rateId}');
    
    Navigator.push(
      context,
      PageRouteBuilder(
        pageBuilder: (context, animation, secondaryAnimation) =>
            SeleccionAsientosPage(
              cantidadBoletos: estudiantes + pasajeros + adultosMayores,
              detalle: detalle,
              unitId: widget.unitId,
              routeUnitScheduleId: widget.routeUnitScheduleId,
              rateId: widget.rateId,
              unitCapacity: _unitCapacity ?? widget.unitCapacity,
              travelDate: widget.travelDate,
              duration: widget.duration,
              origin: widget.origin,
              destination: widget.destination,
              driverName: widget.driverName,
            ),
        transitionsBuilder: (context, animation, secondaryAnimation, child) {
          const begin = Offset(1.0, 0.0);
          const end = Offset.zero;
          const curve = Curves.easeInOut;
          var tween = Tween(
            begin: begin,
            end: end,
          ).chain(CurveTween(curve: curve));
          return SlideTransition(
            position: animation.drive(tween),
            child: child,
          );
        },
      ),
    );
  }

  void _showErrorDialog(String title, String message) {
    showDialog(
      context: context,
      builder: (context) => ScaleTransition(
        scale: CurvedAnimation(
          parent: ModalRoute.of(context)!.animation!,
          curve: Curves.easeOutBack,
        ),
        child: AlertDialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(20),
          ),
          backgroundColor: Colors.white,
          title: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.red.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(
                  Icons.warning_amber_rounded,
                  color: Colors.red,
                  size: 24,
                ),
              ),
              const SizedBox(width: 12),
              Text(
                title,
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                  color: Colors.red,
                ),
              ),
            ],
          ),
          content: Text(message, style: const TextStyle(color: textSecondary)),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              style: TextButton.styleFrom(
                foregroundColor: primaryOrange,
                padding: const EdgeInsets.symmetric(
                  horizontal: 24,
                  vertical: 12,
                ),
              ),
              child: const Text(
                'ENTENDIDO',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),
            ),
          ],
        ),
      ),
    );
  }

  PreferredSizeWidget _buildAppBar() {
    return AppBar(
      backgroundColor: Colors.white,
      elevation: 0,
      leading: IconButton(
        icon: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: Colors.grey[100],
            borderRadius: BorderRadius.circular(10),
          ),
          child: const Icon(
            Icons.arrow_back_rounded,
            color: primaryOrange,
            size: 20,
          ),
        ),
        onPressed: () => Navigator.of(context).pop(),
      ),
      title: Text(
        'Seleccionar Boletos',
        style: TextStyle(
          color: Colors.grey[800],
          fontWeight: FontWeight.bold,
          fontSize: 18,
        ),
      ),
      centerTitle: true,
    );
  }

  Widget _buildProgressIndicator() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              _buildStepIndicator(0, 'Boletos', true),
              _buildStepIndicator(1, 'Asientos', false),
              _buildStepIndicator(2, 'Pago', false),
            ],
          ),
          const SizedBox(height: 12),
          Container(
            height: 4,
            decoration: BoxDecoration(
              color: Colors.grey[200],
              borderRadius: BorderRadius.circular(2),
            ),
            child: AnimatedAlign(
              duration: const Duration(milliseconds: 500),
              alignment: Alignment.centerLeft,
              child: Container(
                width: MediaQuery.of(context).size.width * 0.3,
                height: 4,
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [primaryOrange, darkOrange],
                  ),
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStepIndicator(int step, String title, bool isActive) {
    return Column(
      children: [
        Container(
          width: 32,
          height: 32,
          decoration: BoxDecoration(
            color: isActive ? primaryOrange : Colors.white,
            shape: BoxShape.circle,
            border: Border.all(
              color: isActive ? primaryOrange : Colors.grey[300]!,
              width: 2,
            ),
          ),
          child: Center(
            child: AnimatedSwitcher(
              duration: const Duration(milliseconds: 300),
              child: isActive
                  ? const Icon(
                      Icons.check_rounded,
                      color: Colors.white,
                      size: 16,
                    )
                  : Text(
                      '${step + 1}',
                      key: ValueKey<int>(step),
                      style: TextStyle(
                        color: Colors.grey[600],
                        fontWeight: FontWeight.bold,
                        fontSize: 14,
                      ),
                    ),
            ),
          ),
        ),
        const SizedBox(height: 6),
        Text(
          title,
          style: TextStyle(
            color: isActive ? primaryOrange : Colors.grey[600],
            fontWeight: FontWeight.w600,
            fontSize: 12,
          ),
        ),
      ],
    );
  }

  Widget _buildCombinedHeaderCard() {
    final totalBoletos = estudiantes + pasajeros + adultosMayores;
    final percentage = maxBoletosDisponibles > 0
        ? totalBoletos / maxBoletosDisponibles
        : 1;
    final isOverCapacity = percentage >= 1;

    return ScaleTransition(
      scale: _scaleAnimation,
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 10,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          children: [
            // Información del viaje
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: primaryOrange.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: const Icon(
                    Icons.directions_bus_rounded,
                    color: primaryOrange,
                    size: 18,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        '${widget.origin} → ${widget.destination}',
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 14,
                          color: Colors.black87,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 2),
                      Text(
                        'Conductor: ${widget.driverName}',
                        style: TextStyle(color: Colors.grey[600], fontSize: 11),
                      ),
                    ],
                  ),
                ),
              ],
            ),

            const SizedBox(height: 12),
            const SizedBox(height: 2),
            const SizedBox(height: 12),

            // Información de capacidad
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Asientos disponibles',
                  style: TextStyle(
                    fontWeight: FontWeight.w600,
                    color: Colors.grey[700],
                    fontSize: 13,
                  ),
                ),
                Text(
                  '$maxBoletosDisponibles disponibles',
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    color: isOverCapacity ? Colors.red : primaryOrange,
                    fontSize: 13,
                  ),
                ),
              ],
            ),

            const SizedBox(height: 8),

            // Barra de progreso
            Stack(
              children: [
                Container(
                  height: 4,
                  decoration: BoxDecoration(
                    color: Colors.grey[200],
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
                AnimatedContainer(
                  duration: const Duration(milliseconds: 500),
                  height: 4,
                  width: MediaQuery.of(context).size.width * 0.7 * percentage,
                  decoration: BoxDecoration(
                    gradient: isOverCapacity
                        ? const LinearGradient(
                            colors: [Colors.red, Colors.redAccent],
                          )
                        : const LinearGradient(
                            colors: [primaryOrange, darkOrange],
                          ),
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ],
            ),

            const SizedBox(height: 4),

            // Contador de boletos seleccionados
            if (totalBoletos > 0) ...[
              const SizedBox(height: 8),
              Align(
                alignment: Alignment.centerRight,
                child: Text(
                  '$totalBoletos boleto(s) seleccionado(s)',
                  style: TextStyle(
                    color: primaryOrange,
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildTicketTypeCard({
    required String title,
    required String subtitle,
    required int value,
    required VoidCallback onIncrement,
    required VoidCallback onDecrement,
    required int price,
    required IconData icon,
  }) {
    // Calcular el máximo disponible para este tipo específico - FIEL AL DISEÑO ORIGINAL
    final totalActual = estudiantes + pasajeros + adultosMayores;
    final maxBoletosTipo = maxBoletosDisponibles - totalActual + value;

    // Determinar el mínimo basado en el tipo de boleto - FIEL AL DISEÑO ORIGINAL
    final minValue = 0; // Todos pueden ser 0 ahora

    return ScaleTransition(
      scale: _scaleAnimation,
      child: Container(
        margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 24),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.08),
              blurRadius: 15,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Column(
          children: [
            // Header
            Container(
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: primaryOrange.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Icon(icon, color: primaryOrange, size: 20),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          title,
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: Colors.black87,
                          ),
                        ),
                        Text(
                          subtitle,
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[600],
                          ),
                        ),
                      ],
                    ),
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text(
                        '\$$price',
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: primaryOrange,
                        ),
                      ),
                      Text(
                        'c/u',
                        style: TextStyle(fontSize: 10, color: Colors.grey[500]),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            // Controles
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.grey[50],
                borderRadius: const BorderRadius.only(
                  bottomLeft: Radius.circular(16),
                  bottomRight: Radius.circular(16),
                ),
              ),
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Cantidad: $value',
                          style: const TextStyle(
                            fontWeight: FontWeight.w600,
                            color: Colors.black87,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Slider(
                          value: value.toDouble(),
                          min: minValue.toDouble(),
                          max: maxBoletosTipo.toDouble(),
                          divisions: maxBoletosTipo,
                          activeColor: primaryOrange,
                          inactiveColor: Colors.grey[300],
                          onChanged: maxBoletosTipo > 0
                              ? (double newValue) {
                                  setState(() {
                                    if (title == "Estudiante") {
                                      estudiantes = newValue.toInt();
                                    } else if (title == "Pasajero Regular") {
                                      pasajeros = newValue.toInt();
                                    } else {
                                      adultosMayores = newValue.toInt();
                                    }
                                  });
                                }
                              : null,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 16),
                  Row(
                    children: [
                      _buildQuantityButton(
                        icon: Icons.remove,
                        onPressed: value > minValue ? onDecrement : null,
                      ),
                      const SizedBox(width: 12),
                      Container(
                        width: 36,
                        height: 36,
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: Colors.grey[300]!),
                        ),
                        child: Center(
                          child: Text(
                            value.toString(),
                            style: const TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.bold,
                              color: primaryOrange,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      _buildQuantityButton(
                        icon: Icons.add,
                        onPressed: value < maxBoletosTipo ? onIncrement : null,
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildQuantityButton({
    required IconData icon,
    VoidCallback? onPressed,
  }) {
    return Container(
      width: 36,
      height: 36,
      decoration: BoxDecoration(
        color: onPressed != null ? primaryOrange : Colors.grey[300],
        borderRadius: BorderRadius.circular(8),
      ),
      child: IconButton(
        icon: Icon(icon, size: 16, color: Colors.white),
        onPressed: onPressed,
        padding: EdgeInsets.zero,
      ),
    );
  }

  Widget _buildContinueButton() {
    final totalBoletos = estudiantes + pasajeros + adultosMayores;

    return Container(
      margin: const EdgeInsets.all(24),
      child: ElevatedButton(
        onPressed: totalBoletos > 0 ? _continuarAAsientos : null,
        style: ElevatedButton.styleFrom(
          backgroundColor: primaryOrange,
          padding: const EdgeInsets.symmetric(vertical: 16),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(
              'SELECCIONAR ASIENTOS ($totalBoletos)',
              style: const TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.bold,
                fontSize: 16,
              ),
            ),
            const SizedBox(width: 8),
            const Icon(Icons.arrow_forward, size: 20, color: Colors.white),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return Scaffold(
        backgroundColor: backgroundColor,
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const CircularProgressIndicator(color: primaryOrange),
              const SizedBox(height: 20),
              Text(
                'Cargando información...',
                style: TextStyle(color: Colors.grey[600], fontSize: 16),
              ),
            ],
          ),
        ),
      );
    }

    return Scaffold(
      backgroundColor: backgroundColor,
      appBar: _buildAppBar(),
      body: Column(
        children: [
          _buildProgressIndicator(),
          _buildCombinedHeaderCard(),
          Expanded(
            child: SingleChildScrollView(
              child: Column(
                children: [
                  _buildTicketTypeCard(
                    title: "Estudiante",
                    subtitle: "Con credencial vigente",
                    value: estudiantes,
                    onIncrement: () => setState(() => estudiantes++),
                    onDecrement: () => setState(() => estudiantes--),
                    price: tarifaEstudiante,
                    icon: Icons.school,
                  ),
                  _buildTicketTypeCard(
                    title: "Pasajero Regular",
                    subtitle: "Sin descuentos especiales",
                    value: pasajeros,
                    onIncrement: () => setState(() => pasajeros++),
                    onDecrement: () => setState(() => pasajeros--),
                    price: tarifaPasajero,
                    icon: Icons.person,
                  ),
                  _buildTicketTypeCard(
                    title: "Adulto Mayor",
                    subtitle: "Mayores de 60 años",
                    value: adultosMayores,
                    onIncrement: () => setState(() => adultosMayores++),
                    onDecrement: () => setState(() => adultosMayores--),
                    price: tarifaAdultoMayor,
                    icon: Icons.elderly,
                  ),
                  _buildContinueButton(),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}