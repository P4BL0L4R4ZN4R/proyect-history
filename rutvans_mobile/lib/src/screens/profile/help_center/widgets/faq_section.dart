import 'package:flutter/material.dart';
import 'package:rutvans_mobile/src/constants/constant_colors.dart';
import 'package:rutvans_mobile/src/screens/profile/help_center/faq_item_model.dart';
import 'package:rutvans_mobile/src/screens/profile/help_center/widgets/faq_item_card.dart';
import 'package:rutvans_mobile/src/screens/profile/help_center/widgets/shared_decorations.dart';

class FAQSection extends StatelessWidget {
  final List<FAQItem> faqs;
  final bool isLoading;
  final String? errorMessage;

  const FAQSection({
    super.key,
    required this.faqs,
    required this.isLoading,
    required this.errorMessage,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: buildCardDecoration(ConstantColors.colorCardBorder),
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: const [
              Icon(Icons.help_outline, color: ConstantColors.colorCardTitle),
              SizedBox(width: 8),
              Text(
                'Preguntas frecuentes',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: ConstantColors.colorCardTitle,
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          if (isLoading)
            const Center(
              child: Column(
                children: [
                  SizedBox(height: 16),
                  CircularProgressIndicator(
                    valueColor: AlwaysStoppedAnimation(ConstantColors.colorIcon),
                  ),
                ]
              )
            )
          else if (errorMessage != null)
            Text(
              errorMessage!,
              style: const TextStyle(fontSize: 14, color: ConstantColors.colorError),
            )
          else
            ...faqs.map((faq) => FAQItemCard(faq: faq)),
        ],
      ),
    );
  }
}