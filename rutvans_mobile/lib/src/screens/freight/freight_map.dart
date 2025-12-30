import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'freight_utils.dart';

class FreightMap extends StatelessWidget {
  final MapController mapController;
  final List<Marker> markers;
  final Function(LatLng) onLocationSelected;
  final bool fullScreen;
  final bool selectingOrigin;
  final VoidCallback onToggleFullScreen;
  final VoidCallback onLocateUser;

  const FreightMap({
    super.key,
    required this.mapController,
    required this.markers,
    required this.onLocationSelected,
    this.fullScreen = false,
    this.selectingOrigin = true,
    required this.onToggleFullScreen,
    required this.onLocateUser,
  });

  @override
  Widget build(BuildContext context) {
    if (fullScreen) {
      return _buildFullScreenMap();
    } else {
      return _buildNormalMap();
    }
  }

  Widget _buildNormalMap() {
    return Container(
      height: 250,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.2),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(16),
        child: Stack(
          children: [
            FlutterMap(
              mapController: mapController,
              options: MapOptions(
                initialCenter: const LatLng(20.97, -89.62),
                initialZoom: 9.0,
                maxZoom: 12.0,
                minZoom: 6.0,
                onTap: (tapPosition, point) => onLocationSelected(point),
              ),
              children: [
                TileLayer(
                  urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                  userAgentPackageName: 'com.example.app',
                ),
                MarkerLayer(markers: markers),
              ],
            ),
            Positioned(
              top: 10,
              right: 10,
              child: FloatingActionButton(
                onPressed: onToggleFullScreen,
                backgroundColor: primaryOrange.withValues(alpha: 0.9),
                mini: true,
                child: const Icon(Icons.fullscreen, color: Colors.white),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildFullScreenMap() {
    return Stack(
      children: [
        FlutterMap(
          mapController: mapController,
          options: MapOptions(
            initialCenter: const LatLng(20.97, -89.62),
            initialZoom: 9.0,
            maxZoom: 12.0,
            minZoom: 6.0,
            onTap: (tapPosition, point) => onLocationSelected(point),
          ),
          children: [
            TileLayer(
              urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
              userAgentPackageName: 'com.example.app',
            ),
            MarkerLayer(markers: markers),
          ],
        ),
        Positioned(
          top: 40,
          left: 20,
          child: FloatingActionButton(
            onPressed: onToggleFullScreen,
            backgroundColor: Colors.black.withValues(alpha: 0.7),
            child: const Icon(Icons.arrow_back, color: Colors.white),
          ),
        ),
        Positioned(
          top: 40,
          right: 20,
          child: Column(
            children: [
              FloatingActionButton(
                onPressed: onLocateUser,
                backgroundColor: Colors.black.withValues(alpha: 0.7),
                mini: true,
                child: const Icon(Icons.my_location, color: Colors.white),
              ),
              const SizedBox(height: 10),
              FloatingActionButton(
                onPressed: () {},
                backgroundColor: selectingOrigin ? Colors.green : Colors.green.withValues(alpha: 0.6),
                mini: true,
                child: const Icon(Icons.place, color: Colors.white),
              ),
              const SizedBox(height: 10),
              FloatingActionButton(
                onPressed: () {},
                backgroundColor: !selectingOrigin ? Colors.red : Colors.red.withValues(alpha: 0.6),
                mini: true,
                child: const Icon(Icons.flag, color: Colors.white),
              ),
            ],
          ),
        ),
        Positioned(
          bottom: 20,
          left: 20,
          right: 20,
          child: Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.black.withValues(alpha: 0.8),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Text(
              selectingOrigin 
                ? 'Seleccionando ORIGEN - Toca en el mapa'
                : 'Seleccionando DESTINO - Toca en el mapa',
              textAlign: TextAlign.center,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ),
      ],
    );
  }
}