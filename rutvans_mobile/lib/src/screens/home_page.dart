import 'package:flutter/material.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:rutvans_mobile/src/screens/travel/departure_page.dart';
import 'package:rutvans_mobile/src/screens/payment/shipping_page.dart';
import 'package:rutvans_mobile/src/screens/freight/freight_page.dart';
import 'package:rutvans_mobile/src/screens/profile/history.dart';
import 'package:rutvans_mobile/src/widgets/custom_appbar.dart';
import 'package:rutvans_mobile/src/widgets/custom_navbar.dart';
import 'package:rutvans_mobile/src/widgets/trip_widget.dart';
import 'package:rutvans_mobile/src/services/travel_services/departure_service.dart';
import 'package:intl/intl.dart';

const Color primaryOrange = Color(0xFFFF9800);
const Color primaryWhite = Color(0xFFFFFFFF);
const Color backgroundLight = Color(0xFFF8FAFD);
const Color cardLight = Color(0xFFFFFFFF);
const Color textPrimary = Color(0xFF1E2A3B);
const Color textSecondary = Color(0xFF6B7280);
const Color accentBlue = Color(0xFF3B82F6);
const Color accentGreen = Color(0xFF10B981);

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  int _selectedIndex = 0;
  String _headerTitle = "RUTVANS";
  String? _originFilter;
  String? _destinationFilter;
  late List<Widget> _screens;

  @override
  void initState() {
    super.initState();
    _screens = [
      HomeContent(
        onItemTapped: _onItemTapped,
        onRouteSelected: _onRouteSelected,
      ),
      DeparturePage(origin: _originFilter, destination: _destinationFilter),
      const ShippingPage(),
      const RentPage(),
    ];
  }

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
      _headerTitle = index == 1
          ? "Salidas"
          : index == 2
              ? "Envíos"
              : index == 3
                  ? "Renta"
                  : "RUTVANS";
      if (index != 1) {
        _originFilter = null;
        _destinationFilter = null;
      }
      _screens[1] = DeparturePage(
        origin: _originFilter,
        destination: _destinationFilter,
      );
    });
  }

  void _onRouteSelected(String origin, String destination) {
    setState(() {
      _selectedIndex = 1;
      _headerTitle = "Salidas";
      _originFilter = origin;
      _destinationFilter = destination;
      _screens[1] = DeparturePage(
        origin: _originFilter,
        destination: _destinationFilter,
      );
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white, // NAVBAR ORIGINAL RESTAURADO
      appBar: CustomAppBar(title: _headerTitle, selectedIndex: _selectedIndex),
      body: Column(
        children: [
          Stack(
            children: [
              Container(height: 1, color: Colors.grey[300]),
              Align(
                alignment: Alignment.centerLeft,
                child: Container(height: 3, width: 80, color: primaryOrange),
              ),
            ],
          ),
          Expanded(child: _screens[_selectedIndex]),
        ],
      ),
      bottomNavigationBar: CustomNavBar( // NAVBAR ORIGINAL
        selectedIndex: _selectedIndex,
        onItemTapped: _onItemTapped,
      ),
    );
  }
}

class HomeContent extends StatefulWidget {
  final Function(int) onItemTapped;
  final Function(String, String) onRouteSelected;

  const HomeContent({
    super.key,
    required this.onItemTapped,
    required this.onRouteSelected,
  });

  @override
  State<HomeContent> createState() => _HomeContentState();
}

class _HomeContentState extends State<HomeContent> {
  int animatedCount = 0;
  int _currentCarouselIndex = 0;
  final List<Map<String, dynamic>> _services = [
    {
      'title': 'Viajar',
      'subtitle': 'Compra tu boleto',
      'icon': Icons.airline_seat_recline_normal_rounded,
      'color': primaryOrange,
      'action': 'travel',
    },
    {
      'title': 'Enviar',
      'subtitle': 'Paquetes y carga',
      'icon': Icons.local_shipping_rounded,
      'color': accentBlue,
      'action': 'shipping',
    },
    {
      'title': 'Rentar',
      'subtitle': 'Transporte privado',
      'icon': Icons.directions_car_rounded,
      'color': accentGreen,
      'action': 'rent',
    },
  ];

  // Variables para el RouteFilter
  final DepartureService _departureService = DepartureService();
  List<String> _origins = [];
  List<String> _destinations = [];
  String? _selectedOrigin;
  String? _selectedDestination;
  bool _loadingOrigins = true;
  bool _loadingDestinations = false;

  @override
  void initState() {
    super.initState();
    _loadOrigins();
    Future.delayed(const Duration(milliseconds: 500), () {
      setState(() {
        animatedCount = 1242; // NÚMERO ORIGINAL
      });
    });
  }

  // Cargar orígenes disponibles
  Future<void> _loadOrigins() async {
    try {
      setState(() {
        _loadingOrigins = true;
      });

      final origins = await _departureServiceSafeGetOrigins();

      setState(() {
        _origins = origins;
        _loadingOrigins = false;
      });
    } catch (e) {
      setState(() {
        _origins = [];
        _loadingOrigins = false;
      });
    }
  }

  // Encapsulado para evitar error si el servicio falla por null
  Future<List<String>> _departureServiceSafeGetOrigins() async {
    try {
      final origins = await _departureService.getAvailableOrigins();
      return origins;
    } catch (e) {
      debugPrint('Error service getAvailableOrigins: $e');
      return [];
    }
  }

  Future<void> _loadDestinations(String origin) async {
    setState(() {
      _loadingDestinations = true;
      _destinations = [];
      _selectedDestination = null;
    });
    try {
      final destinations = await _departureService.getAvailableDestinations(origin);
      setState(() {
        _destinations = destinations;
        _loadingDestinations = false;
      });
    } catch (e) {
      setState(() {
        _destinations = [];
        _loadingDestinations = false;
      });
    }
  }

  void _showQuickSearchSheet() {
    String? selectedOrigin;
    String? selectedDestination;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setState) {
            return Container(
              height: MediaQuery.of(context).size.height * 0.85,
              decoration: BoxDecoration(
                color: primaryWhite,
                borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(32),
                  topRight: Radius.circular(32),
                ),
              ),
              child: Column(
                children: [
                  // Header
                  Container(
                    padding: EdgeInsets.all(24),
                    decoration: BoxDecoration(
                      color: primaryWhite,
                      borderRadius: BorderRadius.only(
                        topLeft: Radius.circular(32),
                        topRight: Radius.circular(32),
                      ),
                    ),
                    child: Column(
                      children: [
                        Container(
                          width: 40,
                          height: 4,
                          decoration: BoxDecoration(
                            color: Colors.grey[300],
                            borderRadius: BorderRadius.circular(2),
                          ),
                        ),
                        SizedBox(height: 16),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              '¿A dónde vas?',
                              style: TextStyle(
                                fontSize: 24,
                                fontWeight: FontWeight.bold,
                                color: textPrimary,
                              ),
                            ),
                            IconButton(
                              onPressed: () => Navigator.pop(context),
                              icon: Icon(Icons.close, color: textSecondary),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                  // Content
                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 24),
                      child: Column(
                        children: [
                          // Origin
                          _buildLocationField(
                            icon: Icons.place_rounded,
                            label: 'Origen',
                            value: selectedOrigin,
                            onChanged: (value) {
                              setState(() {
                                selectedOrigin = value;
                                if (selectedDestination == value) {
                                  selectedDestination = null;
                                }
                              });
                            },
                          ),

                          SizedBox(height: 16),

                          // Destination
                          _buildLocationField(
                            icon: Icons.flag_rounded,
                            label: 'Destino',
                            value: selectedDestination,
                            onChanged: (value) {
                              setState(() {
                                selectedDestination = value;
                              });
                            },
                            enabled: selectedOrigin != null,
                          ),

                          SizedBox(height: 32),

                          // Quick destinations
                          _buildQuickDestinations(
                            setState,
                            selectedOrigin,
                            selectedDestination,
                          ),

                          Spacer(),

                          // Search button
                          Container(
                            width: double.infinity,
                            height: 56,
                            child: ElevatedButton(
                              onPressed:
                                  selectedOrigin != null &&
                                      selectedDestination != null
                                  ? () {
                                      Navigator.pop(context);
                                      widget.onRouteSelected(
                                        selectedOrigin!,
                                        selectedDestination!,
                                      );
                                    }
                                  : null,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: primaryOrange,
                                foregroundColor: primaryWhite,
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(16),
                                ),
                                elevation: 2,
                              ),
                              child: Text(
                                'Buscar Viajes',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                          ),

                          SizedBox(height: 24),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }

  Widget _buildLocationField({
    required IconData icon,
    required String label,
    required String? value,
    required Function(String?) onChanged,
    bool enabled = true,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: backgroundLight,
        borderRadius: BorderRadius.circular(16),
      ),
      child: DropdownButtonFormField<String>(
        value: value,
        onChanged: enabled ? onChanged : null,
        decoration: InputDecoration(
          prefixIcon: Icon(icon, color: primaryOrange),
          labelText: label,
          border: OutlineInputBorder(
            borderSide: BorderSide.none,
            borderRadius: BorderRadius.circular(16),
          ),
        ),
        items: _origins.map((locality) => DropdownMenuItem(value: locality, child: Text(locality))).toList(),
      ),
    );
  }

  Widget _buildQuickDestinations(
    StateSetter setState,
    String? origin,
    String? destination,
  ) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Destinos populares',
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.w600,
            color: textPrimary,
          ),
        ),
        SizedBox(height: 12),
        Wrap(
          spacing: 8,
          runSpacing: 8,
          children: _origins.take(4).map((locality) {
            return FilterChip(
              label: Text(locality),
              selected: destination == locality,
              onSelected: origin != null
                  ? (selected) {
                      setState(() {
                        destination = selected ? locality : null;
                      });
                    }
                  : null,
              backgroundColor: backgroundLight,
              selectedColor: primaryOrange.withOpacity(0.2),
              labelStyle: TextStyle(
                color: destination == locality ? primaryOrange : textPrimary,
              ),
            );
          }).toList(),
        ),
      ],
    );
  }

  void _navigateToDeparturePage() {
    widget.onItemTapped(1);
  }

  // Widget del RouteFilter COMPLETO (como estaba originalmente)
  Widget _buildRouteFilter() {
    return Card(
      elevation: 8,
      color: Colors.white,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
      ),
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Título centrado
            const Text(
              "Buscar viaje",
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: Colors.black87,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 24),

            // Icono de intercambio centrado
            Center(
              child: Material(
                color: Colors.grey[100],
                borderRadius: BorderRadius.circular(20),
                child: InkWell(
                  borderRadius: BorderRadius.circular(20),
                  onTap: () {
                    setState(() {
                      final tmp = _selectedOrigin;
                      _selectedOrigin = _selectedDestination;
                      _selectedDestination = tmp;
                      if (_selectedOrigin != null) {
                        _loadDestinations(_selectedOrigin!);
                      }
                    });
                  },
                  child: const Padding(
                    padding: EdgeInsets.all(12),
                    child: Icon(
                      Icons.swap_vert,
                      size: 24,
                      color: Colors.black54,
                    ),
                  ),
                ),
              ),
            ),
            const SizedBox(height: 24),

            // ORIGEN - Estilo completo original
            DropdownButtonFormField<String>(
              decoration: InputDecoration(
                labelText: '¿De dónde sales?',
                prefixIcon: const Icon(Icons.location_on_outlined, color: primaryOrange),
                filled: true,
                fillColor: Colors.grey[50],
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: Colors.grey[300]!),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: Colors.grey[300]!),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: primaryOrange, width: 2),
                ),
                labelStyle: const TextStyle(color: Colors.black54),
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
              ),
              value: _selectedOrigin,
              items: _loadingOrigins
                  ? const []
                  : _origins.map((String origin) {
                      return DropdownMenuItem<String>(
                        value: origin,
                        child: Text(origin, style: const TextStyle(color: Colors.black87)),
                      );
                    }).toList(),
              onChanged: _loadingOrigins
                  ? null
                  : (value) {
                      setState(() {
                        _selectedOrigin = value;
                        _selectedDestination = null;
                        _destinations = [];
                      });
                      if (value != null) _loadDestinations(value);
                    },
            ),

            const SizedBox(height: 16),

            // DESTINO - Estilo completo original
            DropdownButtonFormField<String>(
              decoration: InputDecoration(
                labelText: '¿A dónde quieres ir?',
                prefixIcon: const Icon(Icons.flag_outlined, color: primaryOrange),
                filled: true,
                fillColor: Colors.grey[50],
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: Colors.grey[300]!),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: Colors.grey[300]!),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: primaryOrange, width: 2),
                ),
                labelStyle: const TextStyle(color: Colors.black54),
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
              ),
              value: _selectedDestination,
              items: (_loadingDestinations || _selectedOrigin == null)
                  ? const []
                  : _destinations.map((String destination) {
                      return DropdownMenuItem<String>(
                        value: destination,
                        child: Text(destination, style: const TextStyle(color: Colors.black87)),
                      );
                    }).toList(),
              onChanged: (_loadingDestinations || _selectedOrigin == null)
                  ? null
                  : (value) {
                      setState(() {
                        _selectedDestination = value;
                      });
                    },
            ),

            const SizedBox(height: 24),

            // Botón BUSCAR VIAJE - Estilo prominente original
            ElevatedButton(
              onPressed: (_selectedOrigin != null && _selectedDestination != null)
                  ? () {
                      widget.onRouteSelected(_selectedOrigin!, _selectedDestination!);
                    }
                  : null,
              style: ElevatedButton.styleFrom(
                backgroundColor: primaryOrange,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                elevation: 2,
                disabledBackgroundColor: Colors.grey[300],
              ),
              child: const Text(
                "BUSCAR VIAJE",
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      physics: BouncingScrollPhysics(),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // CAROUSEL AL INICIO - DISEÑO ORIGINAL COMPLETO
          Container(
            width: double.infinity,
            margin: EdgeInsets.zero,
            child: Stack(
              children: [
                CarouselSlider(
                  options: CarouselOptions(
                    height: 200,
                    autoPlay: true,
                    enlargeCenterPage: true,
                    viewportFraction: 1.0,
                    aspectRatio: 16 / 9,
                    autoPlayInterval: Duration(seconds: 4),
                    autoPlayAnimationDuration: Duration(milliseconds: 800),
                    onPageChanged: (index, reason) {
                      setState(() {
                        _currentCarouselIndex = index;
                      });
                    },
                  ),
                  items:
                      [
                        'assets/images/banner1.jpg',
                        'assets/images/banner2.jpg',
                        'assets/images/banner3.jpg',
                      ].map((imgPath) {
                        return Container(
                          margin: EdgeInsets.zero,
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.zero,
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withOpacity(0.2),
                                blurRadius: 12,
                                offset: Offset(0, 6),
                              ),
                            ],
                          ),
                          child: ClipRRect(
                            borderRadius: BorderRadius.zero,
                            child: Image.asset(
                              imgPath,
                              fit: BoxFit.cover,
                              width: double.infinity,
                            ),
                          ),
                        );
                      }).toList(),
                ),

                // Indicadores del carousel ORIGINAL
                Positioned(
                  bottom: 20,
                  left: 0,
                  right: 0,
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      for (int i = 0; i < 3; i++)
                        Container(
                          width: 10,
                          height: 10,
                          margin: EdgeInsets.symmetric(horizontal: 4),
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: _currentCarouselIndex == i
                                ? primaryWhite
                                : primaryWhite.withOpacity(0.5),
                            border: Border.all(
                              color: _currentCarouselIndex == i
                                  ? primaryOrange
                                  : Colors.transparent,
                              width: 2,
                            ),
                          ),
                        ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          // 🔍 COMPONENTE ROUTE FILTER AGREGADO - DISEÑO COMPLETO ORIGINAL
          Padding(
            padding: const EdgeInsets.all(20),
            child: _buildRouteFilter(),
          ),

          // CONTENIDO PRINCIPAL - DISEÑO ORIGINAL INTACTO
          Padding(
            padding: EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Services Grid - TAMAÑO ORIGINAL
                Text(
                  "Servicios RUTVANS",
                  style: TextStyle(
                    fontSize: 20, // TAMAÑO ORIGINAL
                    fontWeight: FontWeight.bold,
                    color: textPrimary,
                  ),
                ),
                SizedBox(height: 16),

                // Grid de servicios - TAMAÑO ORIGINAL
                Container(
                  width: double.infinity,
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: _services.map((service) {
                      return Expanded(
                        child: Container(
                          margin: EdgeInsets.symmetric(horizontal: 6),
                          child: _buildServiceCard(service), // TAMAÑO ORIGINAL
                        ),
                      );
                    }).toList(),
                  ),
                ),

                SizedBox(height: 24),

                // Banner promocional - DISEÑO ORIGINAL
                GestureDetector(
                  onTap: _navigateToDeparturePage,
                  child: Container(
                    width: double.infinity,
                    height: 140, // ALTURA ORIGINAL
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [primaryOrange, Color(0xFFFFB74D)],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Stack(
                      children: [
                        Positioned(
                          right: -20,
                          top: -20,
                          child: Container(
                            width: 120,
                            height: 120,
                            decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.1),
                              shape: BoxShape.circle,
                            ),
                          ),
                        ),
                        Padding(
                          padding: EdgeInsets.all(20),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Text(
                                "¡Viaja con confianza!",
                                style: TextStyle(
                                  color: Colors.white,
                                  fontSize: 18, // TAMAÑO ORIGINAL
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              SizedBox(height: 8),
                              Text(
                                "Consulta horarios y disponibilidad",
                                style: TextStyle(
                                  color: Colors.white.withOpacity(0.9),
                                  fontSize: 14, // TAMAÑO ORIGINAL
                                ),
                              ),
                              SizedBox(height: 12),
                              Container(
                                padding: EdgeInsets.symmetric(
                                  horizontal: 16,
                                  vertical: 8,
                                ),
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  borderRadius: BorderRadius.circular(12),
                                ),
                                child: Text(
                                  "Ver horarios",
                                  style: TextStyle(
                                    color: primaryOrange,
                                    fontWeight: FontWeight.bold,
                                    fontSize: 12, // TAMAÑO ORIGINAL
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),

                SizedBox(height: 24),

                // Próximo viaje - DISEÑO ORIGINAL
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      "Tu próximo viaje",
                      style: TextStyle(
                        fontSize: 18, // TAMAÑO ORIGINAL
                        fontWeight: FontWeight.bold,
                        color: textPrimary,
                      ),
                    ),
                    TextButton(
                      onPressed: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (_) => const History()),
                        );
                      },
                      child: Text(
                        "Ver todos",
                        style: TextStyle(color: primaryOrange),
                      ),
                    ),
                  ],
                ),
                SizedBox(height: 16),
                TripWidget(),

                SizedBox(height: 24),
                SizedBox(height: 40),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // SERVICE CARD - TAMAÑO ORIGINAL
  Widget _buildServiceCard(Map<String, dynamic> service) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: () {
          switch (service['action']) {
            case 'travel':
              _showQuickSearchSheet();
              break;
            case 'shipping':
              widget.onItemTapped(2);
              break;
            case 'rent':
              widget.onItemTapped(3);
              break;
          }
        },
        borderRadius: BorderRadius.circular(16),
        child: Container(
          height: 120, // ALTURA ORIGINAL
          decoration: BoxDecoration(
            color: primaryWhite,
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.05),
                blurRadius: 10,
                offset: Offset(0, 3),
              ),
            ],
          ),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                width: 50, // TAMAÑO ORIGINAL
                height: 50, // TAMAÑO ORIGINAL
                decoration: BoxDecoration(
                  color: service['color'].withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: Icon(service['icon'], color: service['color'], size: 24), // TAMAÑO ORIGINAL
              ),
              SizedBox(height: 8),
              Text(
                service['title'],
                style: TextStyle(
                  fontWeight: FontWeight.w600,
                  color: textPrimary,
                  fontSize: 14, // TAMAÑO ORIGINAL
                ),
              ),
              SizedBox(height: 4),
              Text(
                service['subtitle'],
                style: TextStyle(color: textSecondary, fontSize: 12), // TAMAÑO ORIGINAL
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
      ),
    );
  }
}