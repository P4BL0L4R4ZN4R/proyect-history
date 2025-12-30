import 'package:flutter/material.dart';
import 'package:rutvans_mobile/src/screens/profile/my_account.dart';

const Color primaryOrange = Color(0xFFFF9800);

class CustomAppBar extends StatelessWidget implements PreferredSizeWidget {
  final String title;
  final int selectedIndex;

  const CustomAppBar({
    super.key,
    required this.title,
    required this.selectedIndex,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      color: const Color.fromARGB(255, 238, 237, 237), // Fondo del AppBar
      padding: EdgeInsets.only(
        top: MediaQuery.of(
          context,
        ).viewPadding.top, // Respeta la barra de estado
        left: 16,
        right: 16,
        bottom: 8, // Padding inferior reducido
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              Image.asset('assets/images/iconLogo.png', width: 50, height: 50),
              const SizedBox(width: 8),
              selectedIndex == 0
                  ? RichText(
                      text: const TextSpan(
                        style: TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                        ),
                        children: [
                          TextSpan(
                            text: "RUT",
                            style: TextStyle(color: Colors.black),
                          ),
                          TextSpan(
                            text: "VANS",
                            style: TextStyle(color: primaryOrange),
                          ),
                        ],
                      ),
                    )
                  : Text(
                      title,
                      style: const TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
            ],
          ),
          Row(
            children: [
              IconButton(
                icon: const Icon(Icons.person, color: primaryOrange, size: 28),
                onPressed: () => Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const MyAccount()),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  @override
  Size get preferredSize => const Size.fromHeight(60.0); // Altura fija del contenido, excluyendo la barra de estado
}
