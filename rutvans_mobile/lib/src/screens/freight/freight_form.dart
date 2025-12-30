import 'package:flutter/material.dart';
import 'freight_utils.dart';
import 'freight_location_card.dart';
import 'freight_datetime_picker.dart';
import 'freight_passenger_slider.dart';

class FreightForm extends StatelessWidget {
  final Map<String, dynamic> originDetails;
  final Map<String, dynamic> destinationDetails;
  final DateTime? selectedStartDate;
  final DateTime? selectedEndDate;
  final TimeOfDay? selectedStartTime;
  final TimeOfDay? selectedEndTime;
  final int selectedPassengers;
  final bool canSelectEndDateTime;
  final bool isDateValid;
  final Function(bool) onSelectLocation;
  final Function(bool) onSelectDate;
  final Function(bool) onSelectTime;
  final Function(int) onPassengersChanged;

  const FreightForm({
    super.key,
    required this.originDetails,
    required this.destinationDetails,
    required this.selectedStartDate,
    required this.selectedEndDate,
    required this.selectedStartTime,
    required this.selectedEndTime,
    required this.selectedPassengers,
    required this.canSelectEndDateTime,
    required this.isDateValid,
    required this.onSelectLocation,
    required this.onSelectDate,
    required this.onSelectTime,
    required this.onPassengersChanged,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        // Location Selection Buttons
        _buildLocationSelectionButtons(),
        const SizedBox(height: 20),

        // Selected Locations
        if (originDetails.isNotEmpty || destinationDetails.isNotEmpty)
          _buildLocationSection(),

        const SizedBox(height: 20),

        // Date and Time Sections
        _buildDateTimeSection('Fecha y Hora de Inicio', Icons.play_arrow, true),
        const SizedBox(height: 20),
        _buildDateTimeSection('Fecha y Hora de Fin', Icons.stop, false),

        if (!isDateValid) _buildDateError(),
        const SizedBox(height: 20),

        // Passengers Section
        _buildPassengerSection(),
      ],
    );
  }

  Widget _buildLocationSelectionButtons() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.1),
            blurRadius: 8,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        children: [
          Text(
            'Seleccionar ubicación',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: textGray,
            ),
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: _buildLocationButton('Origen', Icons.place, Colors.green, true),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildLocationButton('Destino', Icons.flag, Colors.red, false),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildLocationButton(String text, IconData icon, Color color, bool isOrigin) {
    return AnimatedContainer(
      duration: const Duration(milliseconds: 200),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: color.withValues(alpha: 0.3),
          width: 1,
        ),
        color: Colors.white,
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: () => onSelectLocation(isOrigin),
          borderRadius: BorderRadius.circular(12),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(icon, size: 24, color: color),
                const SizedBox(height: 8),
                Text(
                  text,
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    color: textGray,
                    fontSize: 14,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildLocationSection() {
    return _buildSectionCard(
      title: 'Ubicaciones Seleccionadas',
      icon: Icons.location_on,
      child: Column(
        children: [
          if (originDetails.isNotEmpty)
            LocationCard(title: 'Origen', details: originDetails, color: Colors.green),
          if (originDetails.isNotEmpty && destinationDetails.isNotEmpty)
            const SizedBox(height: 16),
          if (destinationDetails.isNotEmpty)
            LocationCard(title: 'Destino', details: destinationDetails, color: Colors.red),
        ],
      ),
    );
  }

  Widget _buildDateTimeSection(String title, IconData icon, bool isStart) {
    return _buildSectionCard(
      title: title,
      icon: icon,
      child: Column(
        children: [
          Row(
            children: [
              Expanded(
                child: DateTimeButton(
                  label: 'Fecha ${isStart ? 'Inicio' : 'Fin'}',
                  value: isStart ? selectedStartDate : selectedEndDate,
                  icon: Icons.calendar_today,
                  onPressed: () => onSelectDate(isStart),
                  enabled: isStart ? true : canSelectEndDateTime,
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: DateTimeButton(
                  label: 'Hora ${isStart ? 'Inicio' : 'Fin'}',
                  value: isStart ? selectedStartTime : selectedEndTime,
                  icon: Icons.schedule,
                  onPressed: () => onSelectTime(isStart),
                  enabled: isStart ? true : canSelectEndDateTime,
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          if (!isStart && !canSelectEndDateTime)
            Text(
              'Selecciona primero fecha y hora de inicio',
              style: TextStyle(
                fontSize: 12,
                color: Colors.orange[700],
                fontStyle: FontStyle.italic,
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildDateError() {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.red[50],
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.red),
      ),
      child: Row(
        children: [
          const Icon(Icons.error_outline, color: Colors.red, size: 20),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              'La fecha/hora de fin debe ser posterior a la de inicio',
              style: TextStyle(
                color: Colors.red[700],
                fontSize: 14,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPassengerSection() {
    return _buildSectionCard(
      title: 'Pasajeros',
      icon: Icons.people,
      child: PassengerSlider(
        onPassengersChanged: onPassengersChanged,
        initialValue: selectedPassengers,
      ),
    );
  }

  Widget _buildSectionCard({required String title, required IconData icon, required Widget child}) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.1),
            blurRadius: 8,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, color: primaryOrange, size: 24),
              const SizedBox(width: 12),
              Text(
                title,
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: textGray,
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          child,
        ],
      ),
    );
  }
}