import 'package:flutter/material.dart';
import 'trip_summary.dart';
// import 'package:rutvans_mobile/services/transport_api_service.dart';
import 'package:rutvans_mobile/src/services/travel_services/seat_selection_service.dart';

const Color primaryOrange = Color(0xFFFF9800);
const Color darkOrange = Color(0xFFE65100);
const Color lightOrange = Color(0xFFFFE0B2);
const Color backgroundColor = Color(0xFFF5F5F5);

class SeleccionAsientosPage extends StatefulWidget {
  final int cantidadBoletos;
  final Map<String, int> detalle;
  final int unitId;
  final int routeUnitScheduleId;
  final int rateId;
  final int unitCapacity;
  final DateTime travelDate;
  final Duration duration;
  final String origin;
  final String destination;
  final String driverName;

  const SeleccionAsientosPage({
    super.key,
    required this.cantidadBoletos,
    required this.detalle,
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
  State<SeleccionAsientosPage> createState() => _SeleccionAsientosPageState();
}

class _SeleccionAsientosPageState extends State<SeleccionAsientosPage> {
  Future<void> _recargarAsientos() async {
    setState(() {
      _isLoading = true;
      _hasError = false;
    });
    await _loadSeatData();
  }

  List<int> asientosOcupados = [];
  List<int> asientosSeleccionados = [];
  int? _unitCapacity;
  bool _isLoading = true;
  bool _hasError = false;

  @override
  void initState() {
    super.initState();
    _unitCapacity = widget.unitCapacity;
    _loadSeatData();
  }

  Future<void> _loadSeatData() async {
    try {
      print('🔍 [SeatSelection._loadSeatData] Iniciando carga de asientos...');
      print(
        '🔍 [SeatSelection._loadSeatData] unitId: ${widget.unitId}, scheduleId: ${widget.routeUnitScheduleId}',
      );

      final occupiedSeats = await SeatSelectionService.getOccupiedSeats(
        widget.unitId.toString(),
        widget.routeUnitScheduleId,
      );

      print(
        '🔍 [SeatSelection._loadSeatData] Asientos ocupados recibidos: $occupiedSeats',
      );

      final parsedSeats = occupiedSeats
          .map((e) => int.tryParse(e.toString()) ?? -1)
          .where((e) => e > 0)
          .toList();

      print(
        '🔍 [SeatSelection._loadSeatData] Asientos ocupados parseados: $parsedSeats',
      );

      setState(() {
        // Siempre usa la capacidad pasada por parámetro
        _unitCapacity = widget.unitCapacity;
        asientosOcupados = parsedSeats;
        _isLoading = false;
        _hasError = false;
      });

      print(
        '🔍 [SeatSelection._loadSeatData] Estado actualizado - asientosOcupados: $asientosOcupados',
      );
    } catch (e) {
      // Si falla la API, muestra todos los asientos como disponibles (sin ocupados)
      setState(() {
        _unitCapacity = widget.unitCapacity;
        asientosOcupados = [];
        _isLoading = false;
        _hasError = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            'No se pudo obtener la información de asientos ocupados. Todos los asientos se mostrarán como disponibles.',
          ),
          backgroundColor: Colors.orange,
        ),
      );
    }
  }

  void _toggleAsiento(int num) {
    if (asientosOcupados.contains(num)) return;

    setState(() {
      if (asientosSeleccionados.contains(num)) {
        asientosSeleccionados.remove(num);
      } else if (asientosSeleccionados.length < widget.cantidadBoletos) {
        asientosSeleccionados.add(num);
      }
    });
  }

  Widget _buildAsiento(int numero) {
    if (_unitCapacity == null || numero > _unitCapacity!) {
      return const SizedBox.shrink();
    }

    final ocupado = asientosOcupados.contains(numero);
    final seleccionado = asientosSeleccionados.contains(numero);

    Color seatColor;
    Color borderColor;
    Color iconColor;
    Color textColor;
    String tooltip;

    if (ocupado) {
      seatColor = Colors.grey.shade400;
      borderColor = Colors.grey.shade700;
      iconColor = Colors.white;
      textColor = Colors.white;
      tooltip = 'Asiento $numero (Ocupado)';
    } else if (seleccionado) {
      seatColor = primaryOrange;
      borderColor = darkOrange;
      iconColor = Colors.white;
      textColor = Colors.white;
      tooltip = 'Asiento $numero (Seleccionado)';
    } else {
      seatColor = Colors.white;
      borderColor = primaryOrange;
      iconColor = Colors.black;
      textColor = Colors.black;
      tooltip = 'Asiento $numero (Disponible)';
    }

    return Tooltip(
      message: tooltip,
      child: Container(
        margin: const EdgeInsets.all(4),
        decoration: BoxDecoration(
          color: seatColor,
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: borderColor, width: 2),
        ),
        child: InkWell(
          onTap: ocupado ? null : () => _toggleAsiento(numero),
          child: Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.chair, color: iconColor),
                Text(
                  '$numero',
                  style: TextStyle(
                    color: textColor,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildAsientosGrid() {
    if (_unitCapacity == null) return const SizedBox.shrink();

    final seatNumbers = SeatSelectionService.getSeatNumbers(_unitCapacity!);
    List<Widget> rows = [];
    for (int i = 0; i < seatNumbers.length; i += 4) {
      List<Widget> seatRow = [];
      for (int j = 0; j < 4; j++) {
        int idx = i + j;
        if (idx >= seatNumbers.length) break;
        seatRow.add(Expanded(child: _buildAsiento(seatNumbers[idx])));
      }
      rows.add(Row(children: seatRow));
      rows.add(const SizedBox(height: 8));
    }
    return Column(children: rows);
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }
    if (_hasError) {
      return Scaffold(
        appBar: AppBar(
          title: const Text('Seleccionar Asientos'),
          backgroundColor: primaryOrange,
        ),
        body: const Center(
          child: Text(
            'No se pudieron cargar los asientos ocupados. Intenta de nuevo más tarde.',
            style: TextStyle(color: Colors.red, fontSize: 16),
            textAlign: TextAlign.center,
          ),
        ),
      );
    }

    // UI principal
    return Scaffold(
      backgroundColor: backgroundColor,
      appBar: AppBar(
        title: Text(
          'Seleccionar Asientos (Capacidad: ${_unitCapacity ?? "-"})',
        ),
        backgroundColor: primaryOrange,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Recargar asientos ocupados',
            onPressed: _recargarAsientos,
          ),
        ],
      ),
      body: Column(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(vertical: 12),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                _buildPaso(1, 'Boletos', true),
                _buildPaso(2, 'Asientos', true),
                _buildPaso(3, 'Pago', false),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(16),
            child: Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  children: [
                    Text(
                      'Selecciona ${widget.cantidadBoletos} asiento(s)',
                      style: const TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Total: \$${widget.detalle['total']}',
                      style: const TextStyle(fontSize: 16, color: darkOrange),
                    ),
                    if (asientosSeleccionados.isNotEmpty) ...[
                      const SizedBox(height: 12),
                      Wrap(
                        spacing: 8,
                        children: asientosSeleccionados
                            .map(
                              (asiento) => Chip(
                                label: Text('Asiento $asiento'),
                                onDeleted: () => _toggleAsiento(asiento),
                              ),
                            )
                            .toList(),
                      ),
                    ],
                  ],
                ),
              ),
            ),
          ),
          const Row(
            mainAxisAlignment: MainAxisAlignment.spaceEvenly,
            children: [
              _LeyendaAsiento(
                color: Colors.white,
                borderColor: primaryOrange,
                texto: 'Disponible',
              ),
              _LeyendaAsiento(
                color: primaryOrange,
                borderColor: darkOrange,
                texto: 'Seleccionado',
              ),
              _LeyendaAsiento(
                color: Colors.grey,
                borderColor: Colors.grey,
                texto: 'Ocupado',
              ),
            ],
          ),
          const SizedBox(height: 16),
          Expanded(
            child: SingleChildScrollView(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: Column(
                  children: [
                    const Text(
                      'PARTE DELANTERA',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: Colors.grey,
                      ),
                    ),
                    const SizedBox(height: 16),
                    _buildAsientosGrid(),
                    const SizedBox(height: 16),
                    Container(
                      padding: const EdgeInsets.symmetric(vertical: 8),
                      decoration: BoxDecoration(
                        color: Colors.grey[200],
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: const Center(child: Text('PASILLO')),
                    ),
                  ],
                ),
              ),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(16),
            child: ElevatedButton(
              onPressed: asientosSeleccionados.length == widget.cantidadBoletos
                  ? _confirmarReserva
                  : null,
              style: ElevatedButton.styleFrom(
                backgroundColor: primaryOrange,
                minimumSize: const Size(double.infinity, 50),
              ),
              child: const Text('CONFIRMAR RESERVA'),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPaso(int numero, String texto, bool completado) {
    return Column(
      children: [
        Container(
          width: 30,
          height: 30,
          decoration: BoxDecoration(
            color: completado ? primaryOrange : Colors.grey[300],
            shape: BoxShape.circle,
          ),
          child: Center(
            child: Text(
              '$numero',
              style: TextStyle(
                color: completado ? Colors.white : Colors.grey[600],
              ),
            ),
          ),
        ),
        const SizedBox(height: 4),
        Text(
          texto,
          style: TextStyle(
            color: completado ? darkOrange : Colors.grey,
            fontSize: 12,
          ),
        ),
      ],
    );
  }

  void _confirmarReserva() {
    // Solo navega a la pantalla de pago/resumen, la reserva real se hará después del pago
    final precioPorAsiento = widget.detalle['total']! / widget.cantidadBoletos;
    print(
      '[DEBUG SeleccionAsientosPage] routeUnitScheduleId: ${widget.routeUnitScheduleId}',
    );
    print('[DEBUG SeleccionAsientosPage] rateId: ${widget.rateId}');
    print('[DEBUG SeleccionAsientosPage] unitId: ${widget.unitId}');
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => ResumenViajePage(
          asientos: asientosSeleccionados,
          detalle: widget.detalle,
          routeUnitScheduleId: widget.routeUnitScheduleId,
          rateId: widget.rateId,
          precioPorAsiento: precioPorAsiento,
          unitId: widget.unitId,
          travelDate: widget.travelDate,
          origin: widget.origin,
          destination: widget.destination,
          duration: widget.duration,
          driverName: widget.driverName,
        ),
      ),
    );
  }
}

class _LeyendaAsiento extends StatelessWidget {
  final Color color;
  final Color borderColor;
  final String texto;

  const _LeyendaAsiento({
    required this.color,
    required this.borderColor,
    required this.texto,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Container(
          width: 16,
          height: 16,
          decoration: BoxDecoration(
            color: color,
            border: Border.all(color: borderColor),
            borderRadius: BorderRadius.circular(4),
          ),
        ),
        const SizedBox(width: 6),
        Text(texto, style: const TextStyle(fontSize: 12)),
      ],
    );
  }
}
