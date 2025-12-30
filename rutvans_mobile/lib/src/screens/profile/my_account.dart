import 'package:flutter/material.dart';
import 'package:rutvans_mobile/main.dart';
import 'package:rutvans_mobile/src/constants/constant_colors.dart';
import 'package:rutvans_mobile/src/screens/profile/freight_requests.dart';
import 'package:rutvans_mobile/src/screens/profile/help_center/help_center.dart';
import 'package:rutvans_mobile/src/screens/profile/history.dart';
import 'package:rutvans_mobile/src/screens/profile/my_favorites.dart';
import 'package:rutvans_mobile/src/screens/profile/password_change.dart';
import 'package:rutvans_mobile/src/screens/profile/personal_data.dart';
import 'package:rutvans_mobile/src/screens/profile/privacy_notice.dart';
import 'package:rutvans_mobile/src/screens/profile/terms_conditions.dart';
import 'package:rutvans_mobile/src/services/profile_services/personal_data_service.dart';
import 'package:rutvans_mobile/src/services/api_service.dart';
import 'package:rutvans_mobile/src/models/user_session.dart';

class MyAccount extends StatefulWidget {
  const MyAccount({super.key});

  @override
  MyAccountState createState() => MyAccountState();
}

class MyAccountState extends State<MyAccount>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _profileFadeAnimation;
  late Animation<Offset> _profileSlideAnimation;
  late Animation<double> _optionsFadeAnimation;
  late Animation<Offset> _optionsSlideAnimation;
  final PersonalDataService _personalDataService = PersonalDataService();
  Map<String, dynamic>? _userData;
  bool _isLoading = true;
  String? _error;
  String? _profilePhotoPath;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: const Duration(milliseconds: 800),
      vsync: this,
    );

    _profileFadeAnimation = Tween<double>(begin: 0, end: 1).animate(
      CurvedAnimation(
        parent: _controller,
        curve: const Interval(0.0, 0.5, curve: Curves.easeOut),
      ),
    );

    _profileSlideAnimation =
        Tween<Offset>(begin: const Offset(0, 0.3), end: Offset.zero).animate(
          CurvedAnimation(
            parent: _controller,
            curve: const Interval(0.0, 0.5, curve: Curves.easeOutCubic),
          ),
        );

    _optionsFadeAnimation = Tween<double>(begin: 0, end: 1).animate(
      CurvedAnimation(
        parent: _controller,
        curve: const Interval(0.3, 0.8, curve: Curves.easeOut),
      ),
    );

    _optionsSlideAnimation =
        Tween<Offset>(begin: const Offset(0, 0.2), end: Offset.zero).animate(
          CurvedAnimation(
            parent: _controller,
            curve: const Interval(0.3, 0.8, curve: Curves.easeOutCubic),
          ),
        );

    _loadUserData();
    _controller.forward();
  }

  Future<void> _loadUserData() async {
    try {
      print('🔍 [MyAccount] Cargando datos de usuario...');
      final userData = await _personalDataService.fetchPersonalData();
      print('🔍 [MyAccount] Datos recibidos: $userData');

      if (mounted) {
        setState(() {
          _userData = userData;

          // Construir URL de la imagen de perfil dinámicamente
          final photoUrl = userData['profile_photo_url'] ?? '';
          final photoPath = userData['profile_photo_path'] ?? '';

          print('🔍 [MyAccount] photoUrl: $photoUrl');
          print('🔍 [MyAccount] photoPath: $photoPath');

          _profilePhotoPath = _buildProfilePhotoUrl(photoUrl, photoPath);
          print('🔍 [MyAccount] URL final de foto: $_profilePhotoPath');

          _isLoading = false;
        });
      }
    } catch (e) {
      print('❌ [MyAccount] Error cargando datos: $e');
      if (mounted) {
        setState(() {
          _error = e.toString();
          _isLoading = false;
        });
      }
    }
  }

  String? _buildProfilePhotoUrl(String photoUrl, String photoPath) {
    print('🔍 [MyAccount._buildProfilePhotoUrl] photoUrl: "$photoUrl"');
    print('🔍 [MyAccount._buildProfilePhotoUrl] photoPath: "$photoPath"');
    
    if (photoUrl.isNotEmpty) {
      print('✅ [MyAccount._buildProfilePhotoUrl] Usando photoUrl: $photoUrl');
      return photoUrl;
    } else if (photoPath.isNotEmpty) {
      // Verificar si photoPath ya es una URL completa
      if (photoPath.startsWith('http://') || photoPath.startsWith('https://')) {
        print('✅ [MyAccount._buildProfilePhotoUrl] photoPath ya es URL completa: $photoPath');
        return photoPath;
      } else {
        // Construir URL base removiendo '/api' de la URL base del ApiService
        final baseUrl = ApiService.baseUrl.replaceAll('/api', '');
        final fullUrl = '$baseUrl$photoPath';
        print('✅ [MyAccount._buildProfilePhotoUrl] Construyendo URL: $fullUrl');
        print('✅ [MyAccount._buildProfilePhotoUrl] baseUrl: $baseUrl');
        return fullUrl;
      }
    }
    print(
      '❌ [MyAccount._buildProfilePhotoUrl] No se encontró foto, retornando null',
    );
    return null;
  }  ImageProvider _getProfileImage() {
    try {
      if (_profilePhotoPath != null && _profilePhotoPath!.isNotEmpty) {
        return NetworkImage(_profilePhotoPath!);
      }
      return const AssetImage('assets/images/randomUser.jpg');
    } catch (e) {
      print('❌ Error cargando imagen de perfil: $e');
      return const AssetImage('assets/images/randomUser.jpg');
    }
  }

  Widget _buildProfileAvatar() {
    print(
      '🔍 [MyAccount._buildProfileAvatar] _profilePhotoPath: $_profilePhotoPath',
    );
    print(
      '🔍 [MyAccount._buildProfileAvatar] Condición imagen: ${_profilePhotoPath != null && _profilePhotoPath!.isNotEmpty}',
    );

    return Container(
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        border: Border.all(
          color: ConstantColors.colorImageBorder.withOpacity(0.3),
          width: 2,
        ),
      ),
      child: CircleAvatar(
        radius: 30,
        backgroundColor: ConstantColors.darkGrey.withOpacity(0.1),
        child: _profilePhotoPath != null && _profilePhotoPath!.isNotEmpty
            ? ClipOval(
                child: Image.network(
                  _profilePhotoPath!,
                  fit: BoxFit.cover,
                  width: 60,
                  height: 60,
                  errorBuilder: (context, error, stackTrace) {
                    print(
                      '❌ [MyAccount] Error cargando imagen de perfil: $error',
                    );
                    print('❌ [MyAccount] URL que falló: $_profilePhotoPath');
                    return _buildDefaultAvatar();
                  },
                  loadingBuilder: (context, child, loadingProgress) {
                    if (loadingProgress == null) return child;
                    return Container(
                      width: 60,
                      height: 60,
                      color: ConstantColors.darkGrey.withOpacity(0.1),
                      child: Center(
                        child: CircularProgressIndicator(
                          value: loadingProgress.expectedTotalBytes != null
                              ? loadingProgress.cumulativeBytesLoaded /
                                    loadingProgress.expectedTotalBytes!
                              : null,
                          color: ConstantColors.colorButtonBackground,
                        ),
                      ),
                    );
                  },
                ),
              )
            : _buildDefaultAvatar(),
      ),
    );
  }

  Widget _buildDefaultAvatar() {
    return Container(
      width: 60,
      height: 60,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: ConstantColors.darkGrey.withOpacity(0.2),
      ),
      child: Icon(
        Icons.person,
        size: 30,
        color: ConstantColors.colorCardTitle.withOpacity(0.6),
      ),
    );
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: ConstantColors.colorPageBackground,
      appBar: AppBar(
        title: const Text(
          'Mi Cuenta',
          style: TextStyle(
            fontWeight: FontWeight.w600,
            fontSize: 18,
            color: ConstantColors.colorAppBarText,
            letterSpacing: 0.5,
          ),
        ),
        backgroundColor: ConstantColors.colorAppBarBackground,
        elevation: 1,
        centerTitle: true,
        iconTheme: const IconThemeData(color: ConstantColors.colorAppBarIcon),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.only(
          top: 24,
          left: 20,
          right: 20,
          bottom: 24,
        ),
        child: Column(
          children: [
            // Tarjeta de perfil - Ocupa todo el ancho con imagen a la izquierda
            FadeTransition(
              opacity: _profileFadeAnimation,
              child: SlideTransition(
                position: _profileSlideAnimation,
                child: Container(
                  width: double.infinity, // Ocupa todo el ancho disponible
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    color: ConstantColors.colorCardBackground,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(
                      color: ConstantColors.colorCardBorder.withOpacity(0.2),
                      width: 1,
                    ),
                  ),
                  child: _isLoading
                      ? const Center(
                          child: CircularProgressIndicator(strokeWidth: 2),
                        )
                      : _error != null
                      ? Center(
                          child: Text(
                            'Error: $_error',
                            style: TextStyle(
                              color: ConstantColors.colorError,
                              fontSize: 14,
                            ),
                          ),
                        )
                      : Row(
                          children: [
                            // Avatar a la izquierda
                            _buildProfileAvatar(),
                            const SizedBox(width: 16),
                            // Datos del usuario a la derecha
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    _userData?['name'] ?? 'Usuario',
                                    style: TextStyle(
                                      fontSize: 18,
                                      fontWeight: FontWeight.w600,
                                      color: ConstantColors.colorCardTitle,
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    _userData?['email'] ?? 'correo@ejemplo.com',
                                    style: TextStyle(
                                      fontSize: 14,
                                      color: ConstantColors.colorCardText
                                          .withOpacity(0.7),
                                      fontWeight: FontWeight.w400,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                ),
              ),
            ),
            const SizedBox(height: 32),

            // Sección de opciones - Diseño más limpio
            FadeTransition(
              opacity: _optionsFadeAnimation,
              child: SlideTransition(
                position: _optionsSlideAnimation,
                child: Column(
                  children: [
                    _buildSectionCard(
                      title: 'Configuración',
                      icon: Icons.settings_outlined,
                      children: [
                        _buildOptionTile(
                          icon: Icons.person_outline,
                          label: 'Datos personales',
                          onTap: () async {
                            final shouldRefresh = await Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => const PersonalData(),
                              ),
                            );
                            print(
                              '🔍 [MyAccount] Regresó de PersonalData con shouldRefresh: $shouldRefresh',
                            );
                            if (shouldRefresh == true && mounted) {
                              print(
                                '🔍 [MyAccount] Actualizando datos de usuario...',
                              );
                              await _loadUserData();
                            }
                          },
                        ),
                        _buildDivider(),
                        _buildOptionTile(
                          icon: Icons.favorite_border,
                          label: 'Mis favoritos',
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => const MyFavorites(),
                              ),
                            );
                          },
                        ),
                        _buildDivider(),
                        _buildOptionTile(
                          icon: Icons.history,
                          label: 'Historial de viajes',
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => const History(),
                              ),
                            );
                          },
                        ),
                        _buildDivider(),
                        _buildOptionTile(
                          icon: Icons.emoji_transportation_outlined,
                          label: 'Solicitudes de fletes',
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => const FreightRequests(),
                              ),
                            );
                          },
                        ),
                        _buildDivider(),
                        _buildOptionTile(
                          icon: Icons.lock_outline,
                          label: 'Cambio de contraseña',
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => const PasswordChange(),
                              ),
                            );
                          },
                        ),
                        _buildDivider(),
                        _buildOptionTile(
                          icon: Icons.folder_shared_outlined,
                          label: 'Administrar solicitudes',
                          onTap: () {},
                        ),
                      ],
                    ),
                    const SizedBox(height: 20),
                    _buildSectionCard(
                      title: 'Ayuda y Legal',
                      icon: Icons.help_outline,
                      children: [
                        _buildOptionTile(
                          icon: Icons.help_center_outlined,
                          label: 'Centro de ayuda',
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => const HelpCenter(),
                              ),
                            );
                          },
                        ),
                        _buildDivider(),
                        _buildOptionTile(
                          icon: Icons.description_outlined,
                          label: 'Términos y condiciones',
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => const TermsConditions(),
                              ),
                            );
                          },
                        ),
                        _buildDivider(),
                        _buildOptionTile(
                          icon: Icons.privacy_tip_outlined,
                          label: 'Aviso de privacidad',
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => const PrivacyNotice(),
                              ),
                            );
                          },
                        ),
                        _buildDivider(),
                        _buildOptionTile(
                          icon: Icons.logout,
                          label: 'Cerrar sesión',
                          isLogout: true,
                          onTap: () {
                            _showLogoutConfirmation(
                              context,
                              ConstantColors.colorAppBarBackground,
                            );
                          },
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionCard({
    required String title,
    required IconData icon,
    required List<Widget> children,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: ConstantColors.colorCardBackground,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: ConstantColors.colorCardBorder.withOpacity(0.2),
          width: 1,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.only(
              left: 20,
              top: 20,
              right: 20,
              bottom: 16,
            ),
            child: Row(
              children: [
                Icon(
                  icon,
                  size: 18,
                  color: ConstantColors.colorCardTitle.withOpacity(0.8),
                ),
                const SizedBox(width: 12),
                Text(
                  title,
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w600,
                    color: ConstantColors.colorCardTitle.withOpacity(0.9),
                    letterSpacing: 0.3,
                  ),
                ),
              ],
            ),
          ),
          ...children,
        ],
      ),
    );
  }

  Widget _buildOptionTile({
    required IconData icon,
    required String label,
    bool isLogout = false,
    VoidCallback? onTap,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(8),
        splashColor: ConstantColors.lightOrange.withOpacity(0.1),
        highlightColor: ConstantColors.lightOrange.withOpacity(0.05),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 20),
          child: Row(
            children: [
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: isLogout
                      ? ConstantColors.colorError.withOpacity(0.1)
                      : ConstantColors.colorIconBackground.withOpacity(0.1),
                ),
                child: Icon(
                  icon,
                  size: 20,
                  color: isLogout
                      ? ConstantColors.colorError
                      : ConstantColors.colorIcon.withOpacity(0.8),
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Text(
                  label,
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w500,
                    color: isLogout
                        ? ConstantColors.colorError
                        : ConstantColors.colorCardText.withOpacity(0.9),
                  ),
                ),
              ),
              Icon(
                Icons.arrow_forward_ios,
                size: 16,
                color: isLogout
                    ? ConstantColors.colorError
                    : ConstantColors.colorIcon.withOpacity(0.6),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDivider() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Divider(
        height: 1,
        thickness: 1,
        color: ConstantColors.colorCardBorder.withOpacity(0.1),
      ),
    );
  }

  void _showLogoutConfirmation(BuildContext context, Color primaryColor) {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        child: Container(
          decoration: BoxDecoration(
            color: ConstantColors.colorPopupBackground,
            borderRadius: BorderRadius.circular(16),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(24),
                child: Icon(
                  Icons.exit_to_app,
                  size: 48,
                  color: ConstantColors.colorError,
                ),
              ),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                child: Column(
                  children: [
                    Text(
                      'Cerrar sesión',
                      style: TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                        color: ConstantColors.colorPopupTitle2,
                      ),
                    ),
                    const SizedBox(height: 16),
                    const Text(
                      '¿Estás seguro que deseas cerrar tu sesión?',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 16,
                        color: ConstantColors.colorPopupText,
                        height: 1.4,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),
              Padding(
                padding: const EdgeInsets.all(24),
                child: Row(
                  children: [
                    Expanded(
                      child: OutlinedButton(
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                          side: BorderSide(
                            color: ConstantColors.colorButtonBorder,
                          ),
                        ),
                        onPressed: () => Navigator.pop(context),
                        child: Text(
                          'Cancelar',
                          style: TextStyle(
                            color: ConstantColors.colorButtonText2,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: ConstantColors.colorError,
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                        ),
                        onPressed: () async {
                          // Llamar al método logout de tu ApiService
                          final success =
                              await ApiService.logout(); // <- tu método existente

                          Navigator.pop(context); // Cierra el diálogo
                          if (success) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(
                                content: const Text(
                                  'Sesión cerrada correctamente',
                                ),
                                backgroundColor: ConstantColors.colorSuccess,
                              ),
                            );
                            Navigator.pushReplacement(
                              context,
                              MaterialPageRoute(
                                builder: (context) => const MyApp(),
                              ),
                            );
                          } else {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(
                                content: const Text(
                                  'No se pudo cerrar la sesión',
                                ),
                                backgroundColor: ConstantColors.colorError,
                              ),
                            );
                          }
                        },
                        child: const Text(
                          'Cerrar sesión',
                          style: TextStyle(color: Colors.white),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
