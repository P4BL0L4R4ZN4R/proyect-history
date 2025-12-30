import 'package:flutter/material.dart';
import 'package:rutvans_mobile/src/constants/constant_colors.dart';
import 'package:rutvans_mobile/src/screens/profile/help_center/faq_item_model.dart';

class FAQItemCard extends StatefulWidget {
  final FAQItem faq;

  const FAQItemCard({super.key, required this.faq});

  @override
  State<FAQItemCard> createState() => _FAQItemCardState();
}

class _FAQItemCardState extends State<FAQItemCard> {
  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.symmetric(vertical: 8),
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(8),
        side: BorderSide(
          color: ConstantColors.colorFaqBorder.withAlpha(51),
          width: 1,
        ),
      ),
      child: ExpansionTile(
        title: Text(
          widget.faq.question,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w500,
            color: ConstantColors.colorFaqText,
          ),
        ),
        trailing: Icon(
          widget.faq.isExpanded ? Icons.expand_less : Icons.expand_more,
          color: ConstantColors.colorFaqIcon,
        ),
        onExpansionChanged: (expanded) {
          setState(() => widget.faq.isExpanded = expanded);
        },
        childrenPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8).copyWith(bottom: 16),
        children: [
          Text(
            widget.faq.answer,
            style: const TextStyle(fontSize: 13, color: ConstantColors.colorFaqText),
          ),
        ],
      ),
    );
  }
}