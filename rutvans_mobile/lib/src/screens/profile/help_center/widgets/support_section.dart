import 'package:flutter/material.dart';
import 'package:rutvans_mobile/src/constants/constant_colors.dart';
import 'package:rutvans_mobile/src/screens/profile/help_center/widgets/call_support_button.dart';
import 'package:rutvans_mobile/src/screens/profile/help_center/widgets/email_support_button.dart';
import 'package:rutvans_mobile/src/screens/profile/help_center/widgets/shared_decorations.dart';

class SupportSection extends StatelessWidget {
  const SupportSection({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: buildCardDecoration(ConstantColors.colorCardBorder),
      padding: const EdgeInsets.all(16),
      child: Column(
        children: [
          Row(
            children: [
              buildIconContainer(
                icon: Icons.headset_mic_outlined,
                color: ConstantColors.colorIcon,
                borderColor: ConstantColors.colorIconBorder,
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Soporte al cliente',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: ConstantColors.colorCardTitle,
                      ),
                    ),
                    const Text(
                      'Nuestro equipo está disponible 24/7',
                      style: TextStyle(fontSize: 12, color: ConstantColors.colorCardText),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  style: ButtonStyle(
                    padding: WidgetStatePropertyAll(EdgeInsets.symmetric(vertical: 12)),
                    side: WidgetStatePropertyAll(BorderSide(color: ConstantColors.colorButtonBorder)),
                    shape: WidgetStatePropertyAll(
                      RoundedRectangleBorder(borderRadius: BorderRadius.all(Radius.circular(8))),
                    ),
                  ),
                  icon: Icon(Icons.chat_outlined, color: ConstantColors.colorButtonText2),
                  label: Text(
                    'CHAT EN VIVO',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                      color: ConstantColors.colorButtonText2,
                    ),
                  ),
                  onPressed: null,
                ),
              ),
              SizedBox(width: 16),
              Expanded(child: CallSupportButton()),
            ],
          ),
          const SizedBox(height: 20),
          const EmailSupportButton(),
        ],
      ),
    );
  }
}