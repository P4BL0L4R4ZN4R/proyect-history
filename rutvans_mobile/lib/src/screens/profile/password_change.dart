import 'package:flutter/material.dart';
import 'package:rutvans_mobile/src/services/profile_services/password_service.dart';

class PasswordChange extends StatefulWidget {
  const PasswordChange({super.key});

  @override
  State<PasswordChange> createState() => _PasswordChangeState();
}

class _PasswordChangeState extends State<PasswordChange> with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _fadeAnimation;
  late Animation<Offset> _slideAnimation;

  final _formKey = GlobalKey<FormState>();
  bool _obscureCurrentPassword = true;
  bool _obscureNewPassword = true;
  bool _obscureConfirmPassword = true;
  bool _isLoading = false;

  final TextEditingController _currentPasswordController = TextEditingController();
  final TextEditingController _newPasswordController = TextEditingController();
  final TextEditingController _confirmPasswordController = TextEditingController();

  final PasswordService _passwordService = PasswordService();
  
  get cardColor => null;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: const Duration(milliseconds: 800),
      vsync: this,
    );

    _fadeAnimation = Tween<double>(begin: 0, end: 1).animate(
      CurvedAnimation(
        parent: _controller,
        curve: Curves.easeOutQuart,
      ),
    );

    _slideAnimation = Tween<Offset>(
      begin: const Offset(0, 0.3),
      end: Offset.zero,
    ).animate(
      CurvedAnimation(
        parent: _controller,
        curve: Curves.easeOutQuart,
      ),
    );

    _controller.forward();
  }

  @override
  void dispose() {
    _controller.dispose();
    _currentPasswordController.dispose();
    _newPasswordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  Future<void> _updatePassword() async {
    try {
      final currentPassword = _currentPasswordController.text.trim();
      final newPassword = _newPasswordController.text.trim();
      
      // Primero verificamos la contraseña actual
      final isValid = await _passwordService.verifyCurrentPassword(currentPassword);

      if (!isValid) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Contraseña actual incorrecta'),
              backgroundColor: Colors.red,
              behavior: SnackBarBehavior.floating,
            ),
          );
        }
        return;
      }

      // Si la contraseña actual es válida, actualizamos la nueva contraseña
      await _passwordService.updatePassword(newPassword);

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('Contraseña actualizada correctamente'),
            backgroundColor: Colors.green,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
          ),
        );
      }

      // Limpiar campos
      _currentPasswordController.clear();
      _newPasswordController.clear();
      _confirmPasswordController.clear();
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error: $e'),
            backgroundColor: Colors.red,
            behavior: SnackBarBehavior.floating,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    const primaryColor = Color(0xFFF57C00);
    const backgroundColor = Color(0xFFFFF3E0);
    const cardColor = Colors.white;
    const textColor = Color(0xFF424242);
    const accentColor = Color(0xFFFFB74D);

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text(
          'Cambiar Contraseña',
          style: TextStyle(
            fontWeight: FontWeight.w700,
            fontSize: 20,
            color: Colors.white,
            letterSpacing: 0.5,
          ),
        ),
        backgroundColor: primaryColor,
        elevation: 0,
        centerTitle: true,
        iconTheme: const IconThemeData(color: Colors.white),
        shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(
            bottom: Radius.circular(20),
          ),
        ),
      ),
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              Color(0xFFFFF3E0),
              Colors.white,
            ],
          ),
        ),
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 32),
          child: FadeTransition(
            opacity: _fadeAnimation,
            child: SlideTransition(
              position: _slideAnimation,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Header Section
                  _buildHeaderSection(primaryColor),
                  const SizedBox(height: 32),
                  
                  // Form Section
                  _buildFormSection(
                    primaryColor, 
                    textColor, 
                    cardColor, 
                    accentColor, 
                    backgroundColor
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHeaderSection(Color primaryColor) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          width: 60,
          height: 60,
          decoration: BoxDecoration(
            gradient: LinearGradient(
              colors: [
                primaryColor.withOpacity(0.1),
                primaryColor.withOpacity(0.2),
              ],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.circular(16),
          ),
          child: Icon(
            Icons.security_rounded,
            size: 32,
            color: primaryColor,
          ),
        ),
        const SizedBox(height: 16),
        Text(
          'Seguridad de la Cuenta',
          style: TextStyle(
            fontSize: 28,
            fontWeight: FontWeight.w800,
            color: Colors.grey[800],
            height: 1.2,
          ),
        ),
        const SizedBox(height: 8),
        Text(
          'Actualiza tu contraseña para mantener tu cuenta segura',
          style: TextStyle(
            fontSize: 16,
            color: Colors.grey[600],
            fontWeight: FontWeight.w400,
          ),
        ),
      ],
    );
  }

  Widget _buildFormSection(
    Color primaryColor, 
    Color textColor, 
    Color cardColor, 
    Color accentColor, 
    Color backgroundColor
  ) {
    return Form(
      key: _formKey,
      child: Column(
        children: [
          // Current Password Card
          _buildPasswordCard(
            title: 'Contraseña Actual',
            controller: _currentPasswordController,
            obscureText: _obscureCurrentPassword,
            onToggleVisibility: () {
              setState(() {
                _obscureCurrentPassword = !_obscureCurrentPassword;
              });
            },
            primaryColor: primaryColor,
            textColor: textColor,
            icon: Icons.lock_clock_rounded,
          ),
          const SizedBox(height: 20),

          // New Password Card
          _buildPasswordCard(
            title: 'Nueva Contraseña',
            controller: _newPasswordController,
            obscureText: _obscureNewPassword,
            onToggleVisibility: () {
              setState(() {
                _obscureNewPassword = !_obscureNewPassword;
              });
            },
            primaryColor: primaryColor,
            textColor: textColor,
            icon: Icons.lock_open_rounded,
          ),
          const SizedBox(height: 20),

          // Confirm Password Card
          _buildPasswordCard(
            title: 'Confirmar Contraseña',
            controller: _confirmPasswordController,
            obscureText: _obscureConfirmPassword,
            onToggleVisibility: () {
              setState(() {
                _obscureConfirmPassword = !_obscureConfirmPassword;
              });
            },
            primaryColor: primaryColor,
            textColor: textColor,
            icon: Icons.lock_reset_rounded,
          ),
          const SizedBox(height: 28),

          // Requirements Card
          _buildRequirementsCard(primaryColor, backgroundColor, textColor),
          const SizedBox(height: 32),

          // Update Button
          _buildUpdateButton(primaryColor),
        ],
      ),
    );
  }

  Widget _buildPasswordCard({
    required String title,
    required TextEditingController controller,
    required bool obscureText,
    required VoidCallback onToggleVisibility,
    required Color primaryColor,
    required Color textColor,
    required IconData icon,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: cardColor,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 20,
            offset: const Offset(0, 4),
          ),
        ],
        border: Border.all(
          color: Colors.grey.withOpacity(0.1),
          width: 1,
        ),
      ),
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: primaryColor.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(
                    icon,
                    size: 20,
                    color: primaryColor,
                  ),
                ),
                const SizedBox(width: 12),
                Text(
                  title,
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: textColor,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: controller,
              obscureText: obscureText,
              style: TextStyle(
                fontSize: 16,
                color: textColor,
                fontWeight: FontWeight.w500,
              ),
              decoration: InputDecoration(
                hintText: 'Ingresa tu $title',
                hintStyle: TextStyle(
                  color: Colors.grey[400],
                  fontWeight: FontWeight.w400,
                ),
                filled: true,
                fillColor: Colors.grey[50],
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide.none,
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(
                    color: primaryColor,
                    width: 2,
                  ),
                ),
                contentPadding: const EdgeInsets.symmetric(
                  horizontal: 16,
                  vertical: 16,
                ),
                suffixIcon: IconButton(
                  icon: Icon(
                    obscureText ? Icons.visibility_off_rounded : Icons.visibility_rounded,
                    color: primaryColor.withOpacity(0.7),
                    size: 22,
                  ),
                  onPressed: onToggleVisibility,
                ),
              ),
              validator: (value) {
                if (title == 'Contraseña Actual') {
                  if (value == null || value.trim().isEmpty) {
                    return 'Por favor ingresa tu contraseña actual';
                  }
                  if (value.trim().length < 6) {
                    return 'La contraseña debe tener al menos 6 caracteres';
                  }
                } else if (title == 'Nueva Contraseña') {
                  if (value == null || value.trim().isEmpty) {
                    return 'Por favor ingresa una nueva contraseña';
                  }
                  if (value.trim().length < 8) {
                    return 'La contraseña debe tener al menos 8 caracteres';
                  }
                  if (!RegExp(r'^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{8,}$').hasMatch(value.trim())) {
                    return 'Debe contener una mayúscula, un número y un carácter especial';
                  }
                } else if (title == 'Confirmar Contraseña') {
                  if (value == null || value.trim().isEmpty) {
                    return 'Por favor confirma tu nueva contraseña';
                  }
                  if (value.trim() != _newPasswordController.text.trim()) {
                    return 'Las contraseñas no coinciden';
                  }
                }
                return null;
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRequirementsCard(Color primaryColor, Color backgroundColor, Color textColor) {
    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        color: primaryColor.withOpacity(0.05),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: primaryColor.withOpacity(0.2),
        ),
      ),
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(
                Icons.info_outline_rounded,
                color: primaryColor,
                size: 20,
              ),
              const SizedBox(width: 8),
              Text(
                'Requisitos de Seguridad',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                  color: primaryColor,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          _buildRequirementItem('Mínimo 8 caracteres', primaryColor),
          _buildRequirementItem('Al menos una letra mayúscula', primaryColor),
          _buildRequirementItem('Al menos un número', primaryColor),
          _buildRequirementItem('Al menos un carácter especial', primaryColor),
        ],
      ),
    );
  }

  Widget _buildRequirementItem(String text, Color primaryColor) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        children: [
          Container(
            width: 20,
            height: 20,
            decoration: BoxDecoration(
              color: primaryColor.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(
              Icons.check_rounded,
              size: 14,
              color: primaryColor,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              text,
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey[700],
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildUpdateButton(Color primaryColor) {
    return SizedBox(
      width: double.infinity,
      child: ElevatedButton(
        style: ElevatedButton.styleFrom(
          backgroundColor: primaryColor,
          foregroundColor: Colors.white,
          padding: const EdgeInsets.symmetric(vertical: 18, horizontal: 24),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          elevation: 2,
          shadowColor: primaryColor.withOpacity(0.3),
        ),
        onPressed: _isLoading
            ? null
            : () async {
                if (_formKey.currentState!.validate()) {
                  setState(() {
                    _isLoading = true;
                  });
                  await _updatePassword();
                  setState(() {
                    _isLoading = false;
                  });
                }
              },
        child: _isLoading
            ? const SizedBox(
                width: 22,
                height: 22,
                child: CircularProgressIndicator(
                  strokeWidth: 2.5,
                  color: Colors.white,
                ),
              )
            : Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.security_update_good_rounded,
                    size: 20,
                  ),
                  const SizedBox(width: 10),
                  const Text(
                    'ACTUALIZAR CONTRASEÑA',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                      letterSpacing: 0.5,
                    ),
                  ),
                ],
              ),
      ),
    );
  }
}