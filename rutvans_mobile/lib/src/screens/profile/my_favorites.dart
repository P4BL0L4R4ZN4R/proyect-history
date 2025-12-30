import 'package:flutter/material.dart';
import 'package:rutvans_mobile/src/services/profile_services/favorites_service.dart';
import 'package:rutvans_mobile/src/services/profile_services/history_service.dart'; // Para cargar viajes
import 'package:rutvans_mobile/src/constants/constant_colors.dart'; // Para consistencia

class MyFavorites extends StatefulWidget {
  const MyFavorites({super.key});

  @override
  State<MyFavorites> createState() => _MyFavoritesState();
}

class _MyFavoritesState extends State<MyFavorites> 
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _fadeAnimation;
  late Animation<Offset> _slideAnimation;
  
  List<Map<String, dynamic>> _favorites = [];
  final FavoritesService _favoritesService = FavoritesService();
  final HistoryService _historyService = HistoryService(); // Para obtener detalles de viajes
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: const Duration(milliseconds: 600),
      vsync: this,
    );

    _fadeAnimation = Tween<double>(begin: 0, end: 1).animate(
      CurvedAnimation(
        parent: _controller,
        curve: Curves.easeOut,
      ),
    );

    _slideAnimation = Tween<Offset>(
      begin: const Offset(0, 0.2),
      end: Offset.zero,
    ).animate(
      CurvedAnimation(
        parent: _controller,
        curve: Curves.easeOutCubic,
      ),
    );

    _loadFavorites();
    _controller.forward();
  }

  Future<void> _loadFavorites() async {
    try {
      // Carga IDs de favoritos locales
      final favoriteIds = await FavoritesService.getAllFavoriteIds();
      if (favoriteIds.isEmpty) {
        if (mounted) {
          setState(() {
            _favorites = [];
            _isLoading = false;
          });
        }
        return;
      }

      // Carga todos los viajes desde history para obtener detalles
      final allTravels = await _historyService.fetchTravelsHistory();
      // Filtra solo los favoritos y mapea
      final favorites = allTravels.where((travel) => favoriteIds.contains(travel['_id'])).toList();
      
      if (mounted) {
        setState(() {
          _favorites = favorites;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error cargando favoritos: $e'),
            backgroundColor: ConstantColors.colorError,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          ),
        );
      }
    }
  }

  Future<void> _toggleFavorite(String travelId) async {
    try {
      await FavoritesService.toggleFavorite(travelId);
      await _loadFavorites(); // Refresca la lista
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Removido de favoritos'),
          backgroundColor: ConstantColors.colorError,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        ),
      );
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error al remover favorito: $e'),
          backgroundColor: ConstantColors.colorError,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        ),
      );
    }
  }

  // Función para abrir el searcher
  void _openSearch() {
    showSearch(
      context: context,
      delegate: FavoritesSearchDelegate(_favorites, _toggleFavorite),
    );
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    const primaryColor = Color(0xFFF57C00);
    const backgroundColor = Color(0xFFFFF3E0);

    return Scaffold(
      backgroundColor: backgroundColor,
      appBar: AppBar(
        title: const Text(
          'Mis Favoritos',
          style: TextStyle(
            fontWeight: FontWeight.w600,
            fontSize: 20,
            color: Colors.white,
          ),
        ),
        backgroundColor: primaryColor,
        elevation: 0,
        centerTitle: true,
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          IconButton(
            icon: const Icon(Icons.search),
            onPressed: _openSearch, // Ahora abre el searcher
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _favorites.isEmpty
              ? _buildEmptyState()
              : SingleChildScrollView(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                  child: FadeTransition(
                    opacity: _fadeAnimation,
                    child: SlideTransition(
                      position: _slideAnimation,
                      child: Column(
                        children: [
                          // Filtros rápidos (mantén como estáticos por ahora)
                          SizedBox(
                            height: 50,
                            child: ListView(
                              scrollDirection: Axis.horizontal,
                              children: [
                                _buildFilterChip('Todas', true),
                                _buildFilterChip('Activas ahora', false),
                                _buildFilterChip('Matutinas', false),
                                _buildFilterChip('Nocturnas', false),
                                _buildFilterChip('Con WiFi', false),
                              ],
                            ),
                          ),
                          const SizedBox(height: 16),
                          
                          // Lista de favoritos
                          ListView.builder(
                            physics: const NeverScrollableScrollPhysics(),
                            shrinkWrap: true,
                            itemCount: _favorites.length,
                            itemBuilder: (context, index) {
                              return _buildCombiCard(_favorites[index]);
                            },
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
    );
  }

  Widget _buildEmptyState() {
    const primaryColor = Color(0xFFF57C00);
    
    return Center(
      child: FadeTransition(
        opacity: _fadeAnimation,
        child: SlideTransition(
          position: _slideAnimation,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                Icons.favorite_border,
                size: 80,
                color: const Color.fromRGBO(245, 124, 0, 0.3),
              ),
              const SizedBox(height: 20),
              Text(
                'No tienes combis favoritas',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: primaryColor,
                ),
              ),
              const SizedBox(height: 12),
              const Padding(
                padding: EdgeInsets.symmetric(horizontal: 40),
                child: Text(
                  'Agrega tus combis preferidas para encontrarlas rápidamente y recibir notificaciones cuando estén cerca.',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 14,
                    color: Color(0xFF757575),
                  ),
                ),
              ),
              const SizedBox(height: 24),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: primaryColor,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 24, vertical: 12),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(20),
                  ),
                ),
                onPressed: () {
                  // Navegar a explorar combis (e.g., history)
                },
                child: const Text(
                  'Explorar combis',
                  style: TextStyle(color: Colors.white),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildFilterChip(String label, bool isSelected) {
    const primaryColor = Color(0xFFF57C00);
    
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: FilterChip(
        label: Text(label),
        selected: isSelected,
        onSelected: (bool selected) {
          // Lógica para filtrar (puedes implementar filtrado local aquí)
        },
        selectedColor: const Color.fromRGBO(245, 124, 0, 0.2),
        backgroundColor: Colors.white,
        checkmarkColor: primaryColor,
        labelStyle: TextStyle(
          color: isSelected ? primaryColor : const Color(0xFF757575),
        ),
        shape: StadiumBorder(
          side: BorderSide(
            color: isSelected ? primaryColor : const Color(0xFFEEEEEE),
          ),
        ),
      ),
    );
  }

  Widget _buildCombiCard(Map<String, dynamic> favorite) {
    const primaryColor = Color(0xFFF57C00);
    const textColor = Color(0xFF424242);
    
    // Mapea campos del viaje (ajusta si es necesario)
    final String nombre = '${favorite['origin']} → ${favorite['destination']}';
    final String ruta = nombre; // O personaliza
    final String horario = '${favorite['date']} • ${favorite['time']}';
    final String imagen = favorite['driver']?['image'] ?? 'assets/images/combi1.jpg';
    final String conductor = favorite['driver']?['name'] ?? 'Desconocido';
    final double calificacion = (favorite['rating'] ?? 0).toDouble();
    final String travelId = favorite['_id'];
    final int ratingInt = calificacion.round(); // Redondea para mostrar estrellas enteras

    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
        side: BorderSide(
          color: const Color.fromRGBO(245, 124, 0, 0.2),
          width: 1,
        ),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: () {
          // Ver detalles del viaje
        },
        child: Padding(
          padding: const EdgeInsets.all(12),
          child: Column(
            children: [
              Row(
                children: [
                  // Imagen (usa _getDriverImage si es base64, pero por simplicidad AssetImage)
                  Container(
                    width: 70,
                    height: 70,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(8),
                      image: DecorationImage(
                        image: AssetImage(imagen),
                        fit: BoxFit.cover,
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Nombre y favorito
                        Row(
                          children: [
                            Expanded(
                              child: Text(
                                nombre,
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                  color: textColor,
                                ),
                              ),
                            ),
                            IconButton(
                              icon: const Icon(
                                Icons.favorite,
                                color: Colors.red,
                              ),
                              onPressed: () => _toggleFavorite(travelId),
                            ),
                          ],
                        ),
                        // Ruta
                        Row(
                          children: [
                            Icon(
                              Icons.alt_route,
                              size: 16,
                              color: primaryColor,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              ruta,
                              style: const TextStyle(fontSize: 13),
                            ),
                          ],
                        ),
                        const SizedBox(height: 4),
                        // Horario
                        Row(
                          children: [
                            Icon(
                              Icons.access_time,
                              size: 16,
                              color: primaryColor,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              horario,
                              style: const TextStyle(fontSize: 13),
                            ),
                          ],
                        ),
                        const SizedBox(height: 4),
                        // Conductor
                        Row(
                          children: [
                            Icon(
                              Icons.person_outline,
                              size: 16,
                              color: primaryColor,
                            ),
                            const SizedBox(width: 4),
                            Expanded(
                              child: Text(
                                conductor,
                                style: const TextStyle(fontSize: 13),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              // Separador
              Divider(
                color: primaryColor.withOpacity(0.3),
                thickness: 1,
              ),
              const SizedBox(height: 8),
              // Estrellas sin fondo (distribuidas en Row)
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 12),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: List.generate(5, (starIndex) {
                    return Icon(
                      starIndex < ratingInt
                          ? Icons.star
                          : Icons.star_border,
                      color: Colors.amber,
                      size: 24,
                    );
                  }),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// SearchDelegate personalizado para filtrar favoritos
class FavoritesSearchDelegate extends SearchDelegate<Map<String, dynamic>?> {
  final List<Map<String, dynamic>> _allFavorites;
  final Function(String) _toggleFavorite; // Para toggle si se necesita

  FavoritesSearchDelegate(this._allFavorites, this._toggleFavorite);

  @override
  List<Widget> buildActions(BuildContext context) {
    return [
      IconButton(
        icon: const Icon(Icons.clear),
        onPressed: () {
          query = '';
        },
      ),
    ];
  }

  @override
  Widget buildLeading(BuildContext context) {
    return IconButton(
      icon: const Icon(Icons.arrow_back),
      onPressed: () {
        close(context, null);
      },
    );
  }

  @override
  Widget buildResults(BuildContext context) {
    // Puedes manejar la selección aquí (e.g., navegar al detalle)
    return const SizedBox.shrink(); // Por ahora, cierra al seleccionar
  }

  @override
  Widget buildSuggestions(BuildContext context) {
    final suggestions = _allFavorites.where((favorite) {
      final nombre = '${favorite['origin']} → ${favorite['destination']}';
      final conductor = favorite['driver']?['name'] ?? '';
      final horario = '${favorite['date']} • ${favorite['time']}';
      return nombre.toLowerCase().contains(query.toLowerCase()) ||
             conductor.toLowerCase().contains(query.toLowerCase()) ||
             horario.toLowerCase().contains(query.toLowerCase());
    }).toList();

    if (suggestions.isEmpty) {
      return const Center(
        child: Text('No se encontraron resultados'),
      );
    }

    return ListView.builder(
      itemCount: suggestions.length,
      itemBuilder: (context, index) {
        final favorite = suggestions[index];
        final nombre = '${favorite['origin']} → ${favorite['destination']}';
        final conductor = favorite['driver']?['name'] ?? 'Desconocido';

        return ListTile(
          leading: const Icon(Icons.directions_bus, color: Color(0xFFF57C00)),
          title: Text(nombre),
          subtitle: Text(conductor),
          onTap: () {
            close(context, favorite); // Cierra y retorna el seleccionado (puedes usarlo para navegar)
          },
        );
      },
    );
  }
}