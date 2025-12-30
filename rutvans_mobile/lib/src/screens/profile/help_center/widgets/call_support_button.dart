import 'package:flutter/material.dart';
import 'package:rutvans_mobile/src/constants/constant_colors.dart';
import 'package:url_launcher/url_launcher.dart';

class CallSupportButton extends StatelessWidget {
  static const String _phoneNumber = '9971389652';

  const CallSupportButton({super.key});

  Future<void> _callSupport(BuildContext context) async {
    final bool? confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        child: Container(
          decoration: BoxDecoration(
            color: ConstantColors.colorPopupBackground,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: ConstantColors.colorPopupBorder.withAlpha(127),
              width: 1.5,
            ),
          ),
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: ConstantColors.colorIconBackground.withAlpha(25),
                  border: Border.all(color: ConstantColors.colorIconBorder, width: 2),
                ),
                child: Icon(Icons.call, size: 40, color: ConstantColors.colorIcon),
              ),
              const SizedBox(height: 20),
              Text(
                '¿Llamar al soporte?',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: ConstantColors.colorPopupTitle,
                ),
              ),
              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
                decoration: BoxDecoration(
                  color: ConstantColors.colorCardBackground,
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: ConstantColors.colorCardBorder2.withAlpha(76)),
                ),
                child: Text(
                  _phoneNumber,
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
              const SizedBox(height: 24),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        side: BorderSide(color: ConstantColors.colorButtonBorder),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                      ),
                      onPressed: () => Navigator.of(ctx).pop(false),
                      child: Text(
                        'Cancelar',
                        style: TextStyle(color: ConstantColors.colorButtonText2),
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: ConstantColors.colorButtonBackground,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                      ),
                      onPressed: () => Navigator.of(ctx).pop(true),
                      child: const Text(
                        'Llamar',
                        style: TextStyle(color: ConstantColors.colorButtonText),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );

    if (confirm == true) {
      final Uri phoneUri = Uri(scheme: 'tel', path: _phoneNumber);
      try {
        final launched = await launchUrl(phoneUri, mode: LaunchMode.externalApplication);
        if (!launched) {
          throw Exception('No se pudo iniciar la llamada');
        }
      } catch (e) {
        if (!context.mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error: ${e.toString()}'),
            backgroundColor: ConstantColors.colorError,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return ElevatedButton.icon(
      icon: const Icon(Icons.call, size: 20),
      label: const Text(
        'LLAMAR',
        style: TextStyle(
          fontSize: 12,
          fontWeight: FontWeight.bold,
        ),
      ),
      style: ElevatedButton.styleFrom(
        backgroundColor: ConstantColors.colorButtonBackground,
        foregroundColor: ConstantColors.colorButtonText,
        padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
      ),
      onPressed: () => _callSupport(context),
    );
  }
}