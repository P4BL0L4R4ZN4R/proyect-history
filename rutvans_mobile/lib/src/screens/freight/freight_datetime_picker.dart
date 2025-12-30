import 'package:flutter/material.dart';
import 'freight_utils.dart';

class DateTimeButton extends StatelessWidget {
  final String label;
  final dynamic value;
  final IconData icon;
  final VoidCallback? onPressed;
  final bool enabled;

  const DateTimeButton({
    super.key,
    required this.label,
    required this.value,
    required this.icon,
    this.onPressed,
    required this.enabled,
  });

  @override
  Widget build(BuildContext context) {
    final isSelected = value != null;
    final displayValue = value == null ? 'Seleccionar' : 
        value is DateTime ? '${value.day}/${value.month}/${value.year}' : 
        '${value.hour.toString().padLeft(2, '0')}:${value.minute.toString().padLeft(2, '0')}';

    return AnimatedContainer(
      duration: const Duration(milliseconds: 200),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        boxShadow: isSelected ? [
          BoxShadow(
            color: primaryOrange.withValues(alpha: 0.2),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ] : null,
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: enabled ? onPressed : null,
          borderRadius: BorderRadius.circular(12),
          child: Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: isSelected ? mediumOrange : (enabled ? Colors.white : Colors.grey[100]),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(
                color: isSelected ? primaryOrange : (enabled ? primaryOrange.withValues(alpha: 0.3) : Colors.grey[300]!),
                width: 1,
              ),
            ),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(
                  icon,
                  size: 20,
                  color: isSelected ? Colors.white : (enabled ? primaryOrange : Colors.grey[500]),
                ),
                const SizedBox(height: 8),
                Text(
                  label,
                  style: TextStyle(
                    fontSize: 12,
                    color: isSelected ? Colors.white : (enabled ? darkGray : Colors.grey[500]),
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  displayValue,
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                    color: isSelected ? Colors.white : (enabled ? primaryOrange : Colors.grey[500]),
                  ),
                  textAlign: TextAlign.center,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}