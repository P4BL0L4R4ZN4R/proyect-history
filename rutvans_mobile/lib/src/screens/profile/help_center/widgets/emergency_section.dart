import 'package:flutter/material.dart';
import 'package:rutvans_mobile/src/constants/constant_colors.dart';
import 'package:rutvans_mobile/src/screens/profile/help_center/widgets/shared_decorations.dart';

class EmergencySection extends StatelessWidget {
  const EmergencySection({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: buildCardDecoration(ConstantColors.colorCardBorder),
      padding: const EdgeInsets.all(16),
      child: Row(
        children: [
          buildIconContainer(
            icon: Icons.emergency_outlined,
            color: ConstantColors.colorIcon2,
            borderColor: ConstantColors.colorIconBorder2,
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Emergencias',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                    color: ConstantColors.colorCardTitle2,
                  ),
                ),
                const SizedBox(height: 4),
                const Text(
                  'Contacto inmediato para situaciones de emergencia',
                  style: TextStyle(fontSize: 12, color: ConstantColors.colorCardText),
                ),
                const SizedBox(height: 8),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: ConstantColors.colorButtonBackground2,
                      padding: const EdgeInsets.symmetric(vertical: 10),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    ),
                    onPressed: () {},
                    child: const Text(
                      'LLAMAR A EMERGENCIAS',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: ConstantColors.colorButtonText,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}