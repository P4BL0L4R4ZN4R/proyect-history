import 'package:flutter/material.dart';

const Color primaryOrange = Color(0xFFFF9800);

class CustomNavBar extends StatelessWidget {
  final int selectedIndex;
  final Function(int) onItemTapped;

  const CustomNavBar({
    super.key,
    required this.selectedIndex,
    required this.onItemTapped,
  });

  @override
  Widget build(BuildContext context) {
    return BottomNavigationBar(
      type: BottomNavigationBarType.fixed,
      backgroundColor: primaryOrange,
      selectedItemColor: Colors.white,
      unselectedItemColor: Colors.white70,
      currentIndex: selectedIndex,
      onTap: onItemTapped,
      items: const [
        BottomNavigationBarItem(icon: Icon(Icons.home), label: "Inicio"),
        BottomNavigationBarItem(
            icon: Icon(Icons.directions_bus_filled), label: "Salidas"),
        BottomNavigationBarItem(icon: Icon(Icons.local_shipping), label: "Envíos"),
        BottomNavigationBarItem(icon: Icon(Icons.vpn_key), label: "Renta"),
      ],
    );
  }
}