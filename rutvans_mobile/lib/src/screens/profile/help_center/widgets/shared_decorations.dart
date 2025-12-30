import 'package:flutter/material.dart';
import 'package:rutvans_mobile/src/constants/constant_colors.dart';

BoxDecoration buildCardDecoration(Color borderColor) {
  return BoxDecoration(
    color: ConstantColors.colorCardBackground,
    borderRadius: BorderRadius.circular(16),
    boxShadow: [
      BoxShadow(
        color: ConstantColors.colorCardShadow.withAlpha(51),
        blurRadius: 10,
        offset: const Offset(0, 4),
      ),
    ],
    border: Border.all(
      color: borderColor.withAlpha(127),
      width: 1.5,
    ),
  );
}

Widget buildIconContainer({
  required IconData icon,
  required Color color,
  required Color borderColor,
  Color? backgroundColor,
}) {
  return Container(
    padding: const EdgeInsets.all(12),
    decoration: BoxDecoration(
      shape: BoxShape.circle,
      color: backgroundColor ?? color.withAlpha(25),
      border: Border.all(color: borderColor, width: 2),
    ),
    child: Icon(icon, size: 30, color: color),
  );
}