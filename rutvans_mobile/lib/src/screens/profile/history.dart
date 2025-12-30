import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:rutvans_mobile/src/constants/constant_colors.dart';
import 'package:rutvans_mobile/src/screens/profile/rate_travel_screen.dart';
import 'package:rutvans_mobile/src/services/profile_services/favorites_service.dart';
import 'package:rutvans_mobile/src/services/profile_services/history_service.dart';

class History extends StatefulWidget {
  const History({super.key});

  @override
  State<History> createState() => _HistoryState();
}

class _HistoryState extends State<History> with SingleTickerProviderStateMixin {
  final HistoryService _historyService = HistoryService();
  final List<Map<String, dynamic>> _travelsHistory = [];
  List<Map<String, dynamic>> _filteredTravels = [];

  late AnimationController _controller;
  late List<Animation<double>> _animations;

  bool _isLoading = true;

  String _selectedPeriod = 'Todos';
  String _selectedStatus = 'Todos';
  String _searchQuery = '';

  static const _periodOptions = [
    'Todos',
    'Hoy',
    'Esta semana',
    'Este mes',
    'Últimos 3 meses',
    'Últimos 6 meses',
    'Este año',
  ];

  static const _statusOptions = [
    'Todos',
    'completed',
    'canceled',
    'delayed',
    'pending',
    'in_progress',
  ];

  static const _statusLabels = {
    'completed': 'Completado',
    'canceled': 'Cancelado',
    'delayed': 'Retrasado',
    'pending': 'Pendiente',
    'in_progress': 'En progreso',
  };

  static const _statusColors = {
    'completed': Color(0xFF00C853),
    'canceled': Color(0xFFF44336),
    'delayed': Color(0xFFFF9800),
    'pending': Color(0xFFFF9800),
    'in_progress': Color(0xFF2196F3),
  };

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: const Duration(milliseconds: 650),
      vsync: this,
    );
    _animations = [];
    _loadTravelsHistory();
  }

  Future<void> _loadTravelsHistory() async {
    try {
      final travels = await _historyService.fetchTravelsHistory();

      if (!mounted) return;

      setState(() {
        _travelsHistory.clear();
        _travelsHistory.addAll(travels);
        _filteredTravels = List.from(travels);
        _initializeAnimations();
        _isLoading = false;
      });

      await _initializeFavorites(travels);
      _controller.forward(from: 0);
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
        _showSnackBar('Error al cargar el historial: $e', isError: true);
      }
    }
  }

  void _initializeAnimations() {
    // previene errores si la lista cambia de tamaño rápidamente
    _animations = List.generate(
      _filteredTravels.length,
      (i) => Tween<double>(begin: 0, end: 1).animate(
        CurvedAnimation(
          parent: _controller,
          curve: Interval(
            (i * 0.06).clamp(0.0, 0.9),
            (0.55 + i * 0.06).clamp(0.0, 1.0),
            curve: Curves.easeOutCubic,
          ),
        ),
      ),
    );
  }

  Map<String, DateTime?> _getDateRangeForPeriod(String period) {
    final now = DateTime.now();
    switch (period) {
      case 'Hoy':
        return {'start': DateTime(now.year, now.month, now.day), 'end': now};
      case 'Esta semana':
        final startOfWeek = now.subtract(Duration(days: now.weekday - 1));
        return {
          'start': DateTime(
            startOfWeek.year,
            startOfWeek.month,
            startOfWeek.day,
          ),
          'end': now,
        };
      case 'Este mes':
        return {'start': DateTime(now.year, now.month, 1), 'end': now};
      case 'Últimos 3 meses':
        return {
          'start': DateTime(now.year, now.month - 3, now.day),
          'end': now,
        };
      case 'Últimos 6 meses':
        return {
          'start': DateTime(now.year, now.month - 6, now.day),
          'end': now,
        };
      case 'Este año':
        return {'start': DateTime(now.year, 1, 1), 'end': now};
      default:
        return {'start': null, 'end': null};
    }
  }

  void _applyFilters() {
    final range = _getDateRangeForPeriod(_selectedPeriod);
    final start = range['start'];
    final end = range['end'];
    final query = _searchQuery.trim().toLowerCase();

    final filtered = _travelsHistory.where((t) {
      final date = _parseDate(t['date']);
      final matchesDate =
          start == null && end == null ||
          (date != null &&
              (start == null || !date.isBefore(start)) &&
              (end == null || !date.isAfter(end)));

      final status = (t['status'] ?? '').toString().toLowerCase();
      final matchesStatus =
          _selectedStatus == 'Todos' || status == _selectedStatus.toLowerCase();

      // búsqueda simple sobre conductor, origen o destino o ruta
      final driverName = (t['driver'] is Map)
          ? (t['driver']['name'] ?? '')
          : '';
      final origin = (t['origin'] ?? '');
      final destination = (t['destination'] ?? '');
      final route = (t['route'] ?? '');

      final combined = '$driverName $origin $destination $route'
          .toString()
          .toLowerCase();
      final matchesSearch = query.isEmpty || combined.contains(query);

      return matchesDate && matchesStatus && matchesSearch;
    }).toList();

    if (!mounted) return;
    setState(() {
      _filteredTravels = filtered;
      _initializeAnimations();
    });
    _controller.forward(from: 0);
  }

  DateTime? _parseDate(dynamic value) {
    if (value == null) return null;
    final s = value.toString();
    if (s.isEmpty) return null;
    return DateTime.tryParse(s) ??
        _tryDateFormat(s, 'yyyy-MM-dd') ??
        _tryDateFormat(s, 'dd/MM/yyyy');
  }

  DateTime? _tryDateFormat(String value, String format) {
    try {
      return DateFormat(format).parse(value);
    } catch (_) {
      return null;
    }
  }

  Future<void> _initializeFavorites(List<Map<String, dynamic>> travels) async {
    try {
      final favIds = await FavoritesService.getAllFavoriteIds();
      if (!mounted) return;
      setState(() {
        for (var t in travels) {
          final id = t['_id']?.toString();
          t['is_favorite'] = id != null && favIds.contains(id);
        }
      });
    } catch (e) {
      debugPrint('Error al inicializar favoritos: $e');
    }
  }

  Future<void> _toggleFavorite(int index) async {
    final id = _filteredTravels[index]['_id']?.toString();
    if (id == null) {
      _showSnackBar('Error: ID de viaje no válido', isError: true);
      return;
    }
    try {
      final nowFavorite = await FavoritesService.toggleFavorite(id);
      if (!mounted) return;
      setState(() {
        _filteredTravels[index]['is_favorite'] = nowFavorite;
        final i = _travelsHistory.indexWhere((t) => t['_id']?.toString() == id);
        if (i != -1) _travelsHistory[i]['is_favorite'] = nowFavorite;
      });
      _showSnackBar(
        nowFavorite ? 'Agregado a favoritos' : 'Eliminado de favoritos',
      );
    } catch (e) {
      _showSnackBar('Error al actualizar favorito: $e', isError: true);
    }
  }

  void _showSnackBar(String message, {bool isError = false}) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: isError ? Colors.red : Colors.green,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      ),
    );
  }

  void _navigateToRateTravel(int index) {
    final travel = _filteredTravels[index];
    final status = (travel['status'] ?? '').toString().toLowerCase();
    if (status != 'completed') {
      _showSnackBar('Solo puedes calificar viajes completados', isError: true);
      return;
    }
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => RateTravelScreen(
          travel: travel,
          onRatingSubmitted: _loadTravelsHistory,
        ),
      ),
    );
  }

  ImageProvider _getDriverImage(dynamic image) {
    if (image == null) return const AssetImage('assets/images/driver1.jpg');
    try {
      final img = image.toString();
      if (img.startsWith('data:image')) {
        return MemoryImage(base64Decode(img.split(',').last));
      } else if (img.startsWith('http')) {
        return NetworkImage(img);
      }
    } catch (_) {}
    return const AssetImage('assets/images/driver1.jpg');
  }

  Color _getStatusColor(String s) =>
      _statusColors[s.toLowerCase()] ?? Colors.grey;
  String _getStatusText(String s) =>
      _statusLabels[s.toLowerCase()] ?? 'Desconocido';

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  // ------------------- UI -------------------

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        backgroundColor: ConstantColors.colorButtonBackground,
        title: const Text(
          "Historial de viajes",
          style: TextStyle(color: Colors.white),
        ),
        centerTitle: true,
        elevation: 2,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : Column(
              children: [
                _buildTopFilters(context),
                Expanded(
                  child: _filteredTravels.isEmpty
                      ? _buildEmptyState()
                      : ListView.builder(
                          padding: const EdgeInsets.symmetric(
                            vertical: 8,
                            horizontal: 8,
                          ),
                          itemCount: _filteredTravels.length,
                          itemBuilder: (context, i) {
                            // si por alguna razón cambió el tamaño de animaciones, lo protegemos
                            final anim = (i < _animations.length)
                                ? _animations[i]
                                : AlwaysStoppedAnimation(1.0)
                                      as Animation<double>;
                            return FadeTransition(
                              opacity: anim,
                              child: SlideTransition(
                                position: Tween<Offset>(
                                  begin: const Offset(0, 0.05),
                                  end: Offset.zero,
                                ).animate(anim),
                                child: _buildTravelCard(i),
                              ),
                            );
                          },
                        ),
                ),
              ],
            ),
    );
  }

  Widget _buildTopFilters(BuildContext context) {
    return Container(
      color: Colors.white,
      padding: const EdgeInsets.fromLTRB(12, 12, 12, 8),
      child: Column(
        children: [
          // Search + clear
          Row(
            children: [
              Expanded(
                child: TextField(
                  onChanged: (v) {
                    _searchQuery = v;
                    _applyFilters();
                  },
                  decoration: InputDecoration(
                    hintText: 'Buscar por conductor, origen o destino',
                    prefixIcon: const Icon(Icons.search),
                    suffixIcon: _searchQuery.isNotEmpty
                        ? GestureDetector(
                            onTap: () {
                              setState(() => _searchQuery = '');
                              _applyFilters();
                            },
                            child: const Icon(Icons.close),
                          )
                        : null,
                    filled: true,
                    fillColor: Colors.grey[50],
                    contentPadding: const EdgeInsets.symmetric(
                      vertical: 12,
                      horizontal: 12,
                    ),
                    border: OutlineInputBorder(
                      borderSide: BorderSide.none,
                      borderRadius: BorderRadius.circular(10),
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 8),
              GestureDetector(
                onTap: _clearFilters,
                child: Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 10,
                  ),
                  decoration: BoxDecoration(
                    color: ConstantColors.colorButtonBackground,
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Row(
                    children: const [
                      Icon(Icons.filter_alt_off, color: Colors.white, size: 18),
                      SizedBox(width: 6),
                      Text('Limpiar', style: TextStyle(color: Colors.white)),
                    ],
                  ),
                ),
              ),
            ],
          ),

          const SizedBox(height: 10),

          // Period chips (scroll horizontal)
          SizedBox(
            height: 40,
            child: ListView.separated(
              scrollDirection: Axis.horizontal,
              itemCount: _periodOptions.length,
              separatorBuilder: (_, __) => const SizedBox(width: 8),
              itemBuilder: (context, idx) {
                final period = _periodOptions[idx];
                final selected = period == _selectedPeriod;
                return ChoiceChip(
                  label: Text(
                    period,
                    style: TextStyle(
                      fontSize: 13,
                      color: selected ? Colors.white : Colors.black87,
                    ),
                  ),
                  selected: selected,
                  onSelected: (_) {
                    setState(() => _selectedPeriod = period);
                    _applyFilters();
                  },
                  selectedColor: ConstantColors.colorButtonBackground,
                  backgroundColor: Colors.grey[100],
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 6,
                  ),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16),
                  ),
                );
              },
            ),
          ),

          const SizedBox(height: 10),

          // Status chips - allow "Todos" or a single status
          SizedBox(
            height: 40,
            child: ListView.separated(
              scrollDirection: Axis.horizontal,
              itemCount: _statusOptions.length,
              separatorBuilder: (_, __) => const SizedBox(width: 8),
              itemBuilder: (context, idx) {
                final status = _statusOptions[idx];
                final label = status == 'Todos'
                    ? 'Todos'
                    : _getStatusText(status);
                final selected = status == _selectedStatus;
                return FilterChip(
                  label: Text(
                    label,
                    style: TextStyle(
                      fontSize: 13,
                      color: selected ? Colors.white : Colors.black87,
                    ),
                  ),
                  selected: selected,
                  onSelected: (_) {
                    setState(() => _selectedStatus = status);
                    _applyFilters();
                  },
                  selectedColor:
                      _statusColors[status]?.withOpacity(0.95) ??
                      ConstantColors.colorButtonBackground,
                  backgroundColor: Colors.grey[100],
                  checkmarkColor: Colors.white,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 6,
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.history_rounded, size: 72, color: Colors.grey[300]),
          const SizedBox(height: 16),
          const Text(
            'No hay viajes registrados.',
            style: TextStyle(fontSize: 16, color: Colors.grey),
          ),
          const SizedBox(height: 8),
          const Text(
            'Ajusta los filtros o realiza una búsqueda.',
            style: TextStyle(color: Colors.grey),
          ),
        ],
      ),
    );
  }

  Widget _buildTravelCard(int i) {
    if (i >= _filteredTravels.length) return const SizedBox.shrink();
    final travel = _filteredTravels[i];
    final driver = travel['driver'] ?? {};
    final status = (travel['status'] ?? '').toString().toLowerCase();
    final date = _parseDate(travel['date']);
    final dateText = date != null
        ? DateFormat('dd/MM/yyyy  HH:mm').format(date)
        : 'N/A';
    final origin = travel['origin'] ?? '';
    final destination = travel['destination'] ?? '';
    final amount = travel['amount']?.toString() ?? '--';

    final bool completed = status == 'completed';
    final int rating = () {
      final rv = travel['rating'];
      if (rv is int) return rv;
      if (rv is double) return rv.round();
      return 0;
    }();

    return Card(
      elevation: 2,
      margin: const EdgeInsets.symmetric(vertical: 6, horizontal: 6),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
      child: InkWell(
        borderRadius: BorderRadius.circular(14),
        onTap: () => _navigateToRateTravel(i),
        child: Padding(
          padding: const EdgeInsets.all(12),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              CircleAvatar(
                radius: 30,
                backgroundImage: _getDriverImage(driver['image']),
                backgroundColor: Colors.grey[200],
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Nombre conductor + estado
                    Row(
                      children: [
                        Expanded(
                          child: Text(
                            driver['name'] ?? 'Conductor',
                            style: const TextStyle(
                              fontSize: 15,
                              fontWeight: FontWeight.w600,
                            ),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        const SizedBox(width: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 6,
                          ),
                          decoration: BoxDecoration(
                            color: _getStatusColor(status).withOpacity(0.12),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            _getStatusText(status),
                            style: TextStyle(
                              color: _getStatusColor(status),
                              fontWeight: FontWeight.bold,
                              fontSize: 12,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    // Origen → Destino
                    Text(
                      '$origin → $destination',
                      style: const TextStyle(
                        fontSize: 13,
                        color: Colors.black87,
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 6),
                    // Fecha y precio
                    Row(
                      children: [
                        const Icon(
                          Icons.access_time,
                          size: 14,
                          color: Colors.grey,
                        ),
                        const SizedBox(width: 6),
                        Flexible(
                          child: Text(
                            dateText,
                            style: const TextStyle(
                              fontSize: 12,
                              color: Colors.grey,
                            ),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        const SizedBox(width: 12),
                        const Icon(
                          Icons.monetization_on_outlined,
                          size: 14,
                          color: Colors.grey,
                        ),
                        const SizedBox(width: 6),
                        Text(
                          amount,
                          style: const TextStyle(
                            fontSize: 12,
                            color: Colors.grey,
                          ),
                        ),
                      ],
                    ),
                    if (completed) ...[
                      const SizedBox(height: 8),
                      Row(
                        children: [
                          Row(
                            children: List.generate(5, (idx) {
                              return Icon(
                                idx < rating
                                    ? Icons.star_rounded
                                    : Icons.star_border_rounded,
                                size: 16,
                                color: rating > 0
                                    ? Colors.amber
                                    : Colors.grey[300],
                              );
                            }),
                          ),
                          const SizedBox(width: 8),
                          Flexible(
                            child: Text(
                              rating == 0 ? 'Sin calificar' : '$rating/5',
                              style: const TextStyle(
                                fontSize: 12,
                                color: Colors.grey,
                              ),
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ],
                ),
              ),
              IconButton(
                onPressed: () => _toggleFavorite(i),
                icon: Icon(
                  travel['is_favorite'] == true
                      ? Icons.favorite
                      : Icons.favorite_border,
                  color: travel['is_favorite'] == true
                      ? Colors.red
                      : Colors.grey[600],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _clearFilters() {
    if (!mounted) return;
    setState(() {
      _selectedPeriod = 'Todos';
      _selectedStatus = 'Todos';
      _searchQuery = '';
      _filteredTravels = List.from(_travelsHistory);
      _initializeAnimations();
    });
    _controller.forward(from: 0);
  }
}
