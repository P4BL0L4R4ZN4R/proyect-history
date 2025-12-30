import 'package:flutter/material.dart';
import 'freight_utils.dart';

class PassengerSlider extends StatefulWidget {
  final ValueChanged<int> onPassengersChanged;
  final int initialValue;

  const PassengerSlider({
    super.key,
    required this.onPassengersChanged,
    this.initialValue = 1,
  });

  @override
  State<PassengerSlider> createState() => _PassengerSliderState();
}

class _PassengerSliderState extends State<PassengerSlider> {
  late double _currentValue;

  @override
  void initState() {
    super.initState();
    _currentValue = widget.initialValue.toDouble();
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Slider(
          value: _currentValue,
          min: 1,
          max: 22,
          divisions: 21,
          label: _currentValue.round().toString(),
          onChanged: (value) {
            setState(() {
              _currentValue = value;
            });
            widget.onPassengersChanged(value.round());
          },
          activeColor: primaryOrange,
          inactiveColor: lightOrange,
        ),
        const SizedBox(height: 8),
        Text(
          '${_currentValue.round()} pasajeros',
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            color: primaryOrange,
          ),
        ),
      ],
    );
  }
}