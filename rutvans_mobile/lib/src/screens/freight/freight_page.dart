import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:geolocator/geolocator.dart';
import 'package:rutvans_mobile/src/screens/home_page.dart' hide primaryOrange;
import 'package:rutvans_mobile/src/services/freight_services/freight_service.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'freight_map.dart';
import 'freight_form.dart';
import 'freight_utils.dart';

class RentPage extends StatefulWidget {
  const RentPage({super.key});

  @override
  State<RentPage> createState() => _RentPageState();
}

class _RentPageState extends State<RentPage> {
  final TextEditingController _originController = TextEditingController();
  final TextEditingController _destinationController = TextEditingController();
  final MapController _mapController = MapController();
  
  TimeOfDay? _selectedStartTime;
  TimeOfDay? _selectedEndTime;
  DateTime? _selectedStartDate;
  DateTime? _selectedEndDate;
  int _selectedPassengers = 1;
  LatLng? _originLocation;
  LatLng? _destinationLocation;
  
  bool _selectingOrigin = true;
  bool _isSubmitting = false;
  bool _mapFullScreen = false;

  Map<String, dynamic> _originDetails = {};
  Map<String, dynamic> _destinationDetails = {};
  final List<Marker> _markers = [];

  // Fechas límite
  DateTime get _minDate => DateTime.now();
  DateTime get _maxDate => DateTime.now().add(const Duration(days: 14));

  bool get _isFormComplete =>
      _originDetails.isNotEmpty &&
      _destinationDetails.isNotEmpty &&
      _selectedStartDate != null &&
      _selectedStartTime != null &&
      _selectedEndDate != null &&
      _selectedEndTime != null;

  bool get _isDateValid {
    if (_selectedStartDate == null || _selectedEndDate == null) return true;
    
    if (_selectedEndDate!.isBefore(_selectedStartDate!)) {
      return false;
    }
    
    if (_selectedEndDate == _selectedStartDate && 
        _selectedStartTime != null && 
        _selectedEndTime != null) {
      final startMinutes = _selectedStartTime!.hour * 60 + _selectedStartTime!.minute;
      final endMinutes = _selectedEndTime!.hour * 60 + _selectedEndTime!.minute;
      return endMinutes > startMinutes;
    }
    
    return true;
  }

  bool get _canSelectEndDateTime => _selectedStartDate != null && _selectedStartTime != null;

  @override
  void initState() {
    super.initState();
    _checkPermissionAndLocate();
  }

  Future<void> _checkPermissionAndLocate() async {
    LocationPermission permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied || permission == LocationPermission.deniedForever) {
      permission = await Geolocator.requestPermission();
    }

    if (permission == LocationPermission.always || permission == LocationPermission.whileInUse) {
      final pos = await Geolocator.getCurrentPosition();
      setState(() {
        _mapController.move(LatLng(pos.latitude, pos.longitude), 15);
      });
    }
  }

  void _selectLocation(LatLng point) async {
    final locationDetails = await _getLocationDetails(point);
    
    setState(() {
      if (_selectingOrigin) {
        _originLocation = point;
        _originDetails = locationDetails;
        _originController.text = formatAddress(locationDetails);
      } else {
        _destinationLocation = point;
        _destinationDetails = locationDetails;
        _destinationController.text = formatAddress(locationDetails);
      }
      
      _updateMarkers();
      
      if (_originLocation != null && _destinationLocation != null && _mapFullScreen) {
        _mapFullScreen = false;
      }
    });
  }

  Future<Map<String, dynamic>> _getLocationDetails(LatLng point) async {
    final url = Uri.parse('https://nominatim.openstreetmap.org/reverse?format=json&lat=${point.latitude}&lon=${point.longitude}&addressdetails=1');

    try {
      final response = await http.get(url, headers: {
        "User-Agent": "rutvans-mobile/1.0"
      });

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final address = data['address'] ?? {};

        return {
          'address': "${address['road'] ?? ''} ${address['house_number'] ?? ''}".trim(),
          'neighborhood': address['neighbourhood'] ?? address['suburb'] ?? '------',
          'city': address['city'] ?? address['town'] ?? address['village'] ?? '------',
          'state': address['state'] ?? '------',
          'postal_code': address['postcode'] ?? '------',
          'country': address['country'] ?? '------',
          'lat': point.latitude,
          'lng': point.longitude,
        };
      } else {
        print("Error: ${response.statusCode}");
      }
    } catch (e) {
      print("Error en geocoding: $e");
    }
    
    return {
      'address': 'Ubicación desconocida',
    };
  }

  void _updateMarkers() {
    _markers.clear();
    if (_originLocation != null) {
      _markers.add(Marker(
        width: 40,
        height: 40,
        point: _originLocation!,
        child: const Icon(Icons.location_on, color: Colors.green, size: 40),
      ));
    }
    if (_destinationLocation != null) {
      _markers.add(Marker(
        width: 40,
        height: 40,
        point: _destinationLocation!,
        child: const Icon(Icons.location_on, color: Colors.red, size: 40),
      ));
    }
  }

  void _resetForm() {
    setState(() {
      _originController.clear();
      _destinationController.clear();
      _selectedStartTime = null;
      _selectedEndTime = null;
      _selectedStartDate = null;
      _selectedEndDate = null;
      _selectedPassengers = 1;
      _originLocation = null;
      _destinationLocation = null;
      _originDetails = {};
      _destinationDetails = {};
      _markers.clear();
      _selectingOrigin = true;
      _isSubmitting = false;
      _mapFullScreen = false;
    });
  }

  void _showSuccessDialog(BuildContext context) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return Dialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          child: Container(
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(20),
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(Icons.check_circle, color: primaryOrange, size: 64),
                const SizedBox(height: 16),
                Text(
                  '¡Solicitud Enviada!',
                  style: TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: primaryOrange,
                  ),
                ),
                const SizedBox(height: 16),
                Text(
                  'Tu solicitud ha sido enviada para revisión y está en espera de ser aceptada por un conductor disponible.',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 16,
                    color: darkGray,
                    height: 1.4,
                  ),
                ),
                const SizedBox(height: 24),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: () {
                      Navigator.of(context, rootNavigator: true).pop();
                      _resetForm();
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => const HomePage()),
                      );
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: primaryOrange,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    child: const Text('Aceptar', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  void _showErrorDialog(BuildContext dialogContext, String message) {
    showDialog(
      context: dialogContext,
      builder: (BuildContext context) {
        return Dialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          child: Container(
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(20),
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(Icons.error_outline, color: Colors.red, size: 64),
                const SizedBox(height: 16),
                Text(
                  'Acción no disponible',
                  style: TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: Colors.red,
                  ),
                ),
                const SizedBox(height: 16),
                Text(
                  message,
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 16,
                    color: darkGray,
                    height: 1.4,
                  ),
                ),
                const SizedBox(height: 24),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: () => Navigator.of(context).pop(),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: primaryOrange,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    child: const Text('Entendido', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  // Métodos para selección de fecha/hora que acepten solo el parámetro booleano
  void _handleSelectDate(bool isStartDate) {
    _selectDate(isStartDate);
  }

  void _handleSelectTime(bool isStartTime) {
    _selectTime(isStartTime);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: lightGray,
      body: _mapFullScreen ? _buildFullScreenMap() : _buildNormalLayout(),
    );
  }

  Widget _buildNormalLayout() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header Section
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.1),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                Text(
                  'Renta de Servicio (Flete)',
                  style: TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                    color: primaryOrange,
                  ),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 8),
                Text(
                  'Selecciona origen y destino en el mapa para programar tu viaje',
                  style: TextStyle(
                    color: darkGray,
                    fontSize: 16,
                  ),
                  textAlign: TextAlign.center,
                ),
              ],
            ),
          ),
          
          const SizedBox(height: 20),

          // Map Section
          FreightMap(
            mapController: _mapController,
            markers: _markers,
            onLocationSelected: _selectLocation,
            fullScreen: false,
            selectingOrigin: _selectingOrigin,
            onToggleFullScreen: () => setState(() => _mapFullScreen = true),
            onLocateUser: _checkPermissionAndLocate,
          ),

          const SizedBox(height: 16),

          // Form Section
          FreightForm(
            originDetails: _originDetails,
            destinationDetails: _destinationDetails,
            selectedStartDate: _selectedStartDate,
            selectedEndDate: _selectedEndDate,
            selectedStartTime: _selectedStartTime,
            selectedEndTime: _selectedEndTime,
            selectedPassengers: _selectedPassengers,
            canSelectEndDateTime: _canSelectEndDateTime,
            isDateValid: _isDateValid,
            onSelectLocation: (isOrigin) => setState(() {
              _selectingOrigin = isOrigin;
              _mapFullScreen = true;
            }),
            onSelectDate: _handleSelectDate,
            onSelectTime: _handleSelectTime,
            onPassengersChanged: (passengers) => setState(() {
              _selectedPassengers = passengers;
            }),
          ),

          const SizedBox(height: 32),

          // Submit Button
          AnimatedContainer(
            duration: const Duration(milliseconds: 300),
            width: double.infinity,
            child: AnimatedScale(
              duration: const Duration(milliseconds: 200),
              scale: _isSubmitting ? 0.95 : 1.0,
              child: ElevatedButton(
                onPressed: (_isFormComplete && _isDateValid && !_isSubmitting)
                    ? () async {
                        setState(() => _isSubmitting = true);
                        
                        final success = await RentService.insertRentRequest({
                          'start_time': _selectedStartTime != null 
                            ? '${_selectedStartTime!.hour.toString().padLeft(2, '0')}:${_selectedStartTime!.minute.toString().padLeft(2, '0')}'
                            : '',
                          'end_time': _selectedEndTime != null 
                            ? '${_selectedEndTime!.hour.toString().padLeft(2, '0')}:${_selectedEndTime!.minute.toString().padLeft(2, '0')}'
                            : '',
                          'start_date': _selectedStartDate?.toIso8601String() ?? '',
                          'end_date': _selectedEndDate?.toIso8601String() ?? '',
                          'origin': _originDetails,
                          'destination': _destinationDetails,
                          'passengers': _selectedPassengers.toString(),
                          'created_at': DateTime.now().toIso8601String(),
                        });

                        setState(() => _isSubmitting = false);
                        
                        if (success) {
                          WidgetsBinding.instance.addPostFrameCallback((_) {
                            _showSuccessDialog(context);
                          });
                        }
                      }
                    : null,
                style: ElevatedButton.styleFrom(
                  backgroundColor: (_isFormComplete && _isDateValid) ? primaryOrange : Colors.grey[400],
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 20),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  elevation: 4,
                  shadowColor: primaryOrange.withValues(alpha: 0.3),
                ),
                child: _isSubmitting
                    ? SizedBox(
                        height: 24,
                        width: 24,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          valueColor: AlwaysStoppedAnimation(Colors.white),
                        ),
                      )
                    : Text(
                        'SOLICITAR SERVICIO',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFullScreenMap() {
    return Stack(
      children: [
        FlutterMap(
          mapController: _mapController,
          options: MapOptions(
            initialCenter: const LatLng(20.97, -89.62),
            initialZoom: 9,
            onTap: (tapPosition, point) => _selectLocation(point),
          ),
          children: [
            TileLayer(
              urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
              userAgentPackageName: 'com.example.app',
            ),
            MarkerLayer(markers: _markers),
          ],
        ),
        Positioned(
          top: 40,
          left: 20,
          child: FloatingActionButton(
            onPressed: () => setState(() => _mapFullScreen = false),
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
                onPressed: _checkPermissionAndLocate,
                backgroundColor: Colors.black.withValues(alpha: 0.7),
                mini: true,
                child: const Icon(Icons.my_location, color: Colors.white),
              ),
              const SizedBox(height: 10),
              FloatingActionButton(
                onPressed: () => setState(() => _selectingOrigin = true),
                backgroundColor: _selectingOrigin ? Colors.green : Colors.green.withValues(alpha: 0.6),
                mini: true,
                child: const Icon(Icons.place, color: Colors.white),
              ),
              const SizedBox(height: 10),
              FloatingActionButton(
                onPressed: () => setState(() => _selectingOrigin = false),
                backgroundColor: !_selectingOrigin ? Colors.red : Colors.red.withValues(alpha: 0.6),
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
              _selectingOrigin 
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

  Future<void> _selectDate(bool isStartDate) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: _getMinDate(isStartDate),
      firstDate: _getMinDate(isStartDate),
      lastDate: _getMaxDate(isStartDate),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: ColorScheme.light(
              primary: primaryOrange,
              onPrimary: Colors.white,
              onSurface: textGray,
            ), dialogTheme: const DialogThemeData(backgroundColor: Colors.white),
          ),
          child: child!,
        );
      },
    );
    
    if (picked != null) {
      setState(() {
        if (isStartDate) {
          _selectedStartDate = picked;
          if (_selectedEndDate != null && _selectedEndDate!.isBefore(picked)) {
            _selectedEndDate = null;
            _selectedEndTime = null;
          }
          if (_selectedEndDate == picked && _selectedEndTime != null && _selectedStartTime != null) {
            final startMinutes = _selectedStartTime!.hour * 60 + _selectedStartTime!.minute;
            final endMinutes = _selectedEndTime!.hour * 60 + _selectedEndTime!.minute;
            if (endMinutes <= startMinutes) {
              _selectedEndTime = null;
            }
          }
        } else {
          _selectedEndDate = picked;
          if (_selectedEndDate == _selectedStartDate && _selectedStartTime != null && _selectedEndTime != null) {
            final startMinutes = _selectedStartTime!.hour * 60 + _selectedStartTime!.minute;
            final endMinutes = _selectedEndTime!.hour * 60 + _selectedEndTime!.minute;
            if (endMinutes <= startMinutes) {
              _selectedEndTime = null;
            }
          }
        }
      });
    }
  }

  Future<void> _selectTime(bool isStartTime) async {
    final TimeOfDay currentlySelected = isStartTime 
        ? _selectedStartTime ?? const TimeOfDay(hour: 12, minute: 0)
        : _selectedEndTime ?? const TimeOfDay(hour: 12, minute: 0);

    final TimeOfDay? picked = await showTimePicker(
      context: context,
      initialTime: currentlySelected,
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: ColorScheme.light(
              primary: primaryOrange,
              onPrimary: Colors.white,
              onSurface: textGray,
            ), dialogTheme: const DialogThemeData(backgroundColor: Colors.white),
          ),
          child: child!,
        );
      },
    );
    
    if (picked != null) {
      if (_isTimeValid(picked, isStartTime)) {
        setState(() {
          if (isStartTime) {
            _selectedStartTime = picked;
            if (_selectedEndDate == _selectedStartDate && _selectedEndTime != null) {
              final startMinutes = picked.hour * 60 + picked.minute;
              final endMinutes = _selectedEndTime!.hour * 60 + _selectedEndTime!.minute;
              if (endMinutes <= startMinutes) {
                _selectedEndTime = null;
              }
            }
          } else {
            _selectedEndTime = picked;
          }
        });
      } else {
        final errorMessage = isStartTime 
            ? 'La hora de inicio debe ser anterior a la hora de fin'
            : 'La hora de fin debe ser posterior a la hora de inicio';
        
        final currentContext = context;
        if (mounted) {
          WidgetsBinding.instance.addPostFrameCallback((_) {
            if (mounted) {
              _showErrorDialog(currentContext, errorMessage);
            }
          });
        }
      }
    }
  }

  DateTime _getMinDate(bool isStartDate) {
    if (isStartDate) {
      return _minDate;
    } else {
      return _selectedStartDate ?? _minDate;
    }
  }

  DateTime _getMaxDate(bool isStartDate) {
    if (isStartDate) {
      return _selectedEndDate ?? _maxDate;
    } else {
      return _maxDate;
    }
  }

  bool _isTimeValid(TimeOfDay selectedTime, bool isStartTime) {
    if (isStartTime) {
      if (_selectedEndDate == _selectedStartDate && _selectedEndTime != null) {
        final selectedMinutes = selectedTime.hour * 60 + selectedTime.minute;
        final endMinutes = _selectedEndTime!.hour * 60 + _selectedEndTime!.minute;
        return selectedMinutes < endMinutes;
      }
      return true;
    } else {
      if (_selectedStartDate == _selectedEndDate && _selectedStartTime != null) {
        final selectedMinutes = selectedTime.hour * 60 + selectedTime.minute;
        final startMinutes = _selectedStartTime!.hour * 60 + _selectedStartTime!.minute;
        return selectedMinutes > startMinutes;
      }
      return true;
    }
  }
}