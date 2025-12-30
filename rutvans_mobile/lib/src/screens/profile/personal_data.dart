import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:rutvans_mobile/src/constants/constant_colors.dart';
import 'package:rutvans_mobile/src/services/profile_services/personal_data_service.dart';
import 'package:rutvans_mobile/src/services/api_service.dart';
import 'dart:io';

class PersonalData extends StatefulWidget {
  const PersonalData({super.key});

  @override
  State<PersonalData> createState() => _PersonalDataState();
}

class _PersonalDataState extends State<PersonalData>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _fadeAnimation;
  late Animation<Offset> _slideAnimation;

  final _formKey = GlobalKey<FormState>();
  bool _isEditing = false;
  bool _isLoading = true;
  bool _hasChanges = false; // Trackear si hubo cambios

  final TextEditingController _nameController = TextEditingController();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _phoneController = TextEditingController();
  final TextEditingController _addressController = TextEditingController();
  String? _profilePhotoPath;

  final PersonalDataService _personalDataService = PersonalDataService();
  final ImagePicker _picker = ImagePicker();
  static const int _maxImageSize = 2 * 1024 * 1024; // 2 MB in bytes

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: const Duration(milliseconds: 800),
      vsync: this,
    );

    _fadeAnimation = Tween<double>(
      begin: 0,
      end: 1,
    ).animate(CurvedAnimation(parent: _controller, curve: Curves.easeOutQuart));

    _slideAnimation = Tween<Offset>(
      begin: const Offset(0, 0.3),
      end: Offset.zero,
    ).animate(CurvedAnimation(parent: _controller, curve: Curves.easeOutQuart));

    _loadPersonalData();
    _controller.forward();
  }

  Future<void> _loadPersonalData() async {
    try {
      final data = await _personalDataService.fetchPersonalData();
      setState(() {
        _nameController.text = data['name'] ?? '';
        _emailController.text = data['email'] ?? '';
        _phoneController.text = data['phone_number'] ?? '';
        _addressController.text = data['address'] ?? '';

        // USAR URL DINÁMICA - Priorizar profile_photo_url si está disponible
        final photoUrl = data['profile_photo_url'] ?? '';
        final photoPath = data['profile_photo_path'] ?? '';

        _profilePhotoPath = _buildProfilePhotoUrl(photoUrl, photoPath);
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error al cargar datos: $e'),
            backgroundColor: ConstantColors.colorError,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
          ),
        );
      }
    }
  }

  String? _buildProfilePhotoUrl(String photoUrl, String photoPath) {
    if (photoUrl.isNotEmpty) {
      return photoUrl;
    } else if (photoPath.isNotEmpty) {
      // Verificar si photoPath ya es una URL completa
      if (photoPath.startsWith('http://') || photoPath.startsWith('https://')) {
        print('✅ [PersonalData._buildProfilePhotoUrl] photoPath ya es URL completa: $photoPath');
        return photoPath;
      } else {
        // Construir URL base removiendo '/api' de la URL base del ApiService
        final baseUrl = ApiService.baseUrl.replaceAll('/api', '');
        final fullUrl = '$baseUrl$photoPath';
        print('✅ [PersonalData._buildProfilePhotoUrl] Construyendo URL: $fullUrl');
        return fullUrl;
      }
    }
    return null;
  }

  Future<void> _pickAndUploadImage() async {
    try {
      print('🔍 [PersonalData] Iniciando selección de imagen...');
      final XFile? image = await _picker.pickImage(source: ImageSource.gallery);
      if (image == null) {
        print('🔍 [PersonalData] Usuario canceló selección de imagen');
        return;
      }

      print('🔍 [PersonalData] Imagen seleccionada: ${image.path}');
      setState(() {
        _isLoading = true;
      });

      final File imageFile = File(image.path);
      final int fileSize = await imageFile.length();
      print('🔍 [PersonalData] Tamaño del archivo: ${fileSize} bytes');

      if (fileSize > _maxImageSize) {
        throw Exception('La imagen excede el tamaño máximo de 2 MB');
      }

      print('🔍 [PersonalData] Iniciando subida de imagen...');
      final String uploadedPath = await _personalDataService.uploadProfilePhoto(
        imageFile,
      );
      print('🔍 [PersonalData] Imagen subida exitosamente: $uploadedPath');

      setState(() {
        // Actualizar solo la foto de perfil - NO GUARDAR AUTOMÁTICAMENTE
        _profilePhotoPath =
            uploadedPath; // Usar directamente la URL que retorna el backend
        _hasChanges = true; // Marcar que hubo cambios
        _isLoading = false;
      });

      // REMOVIDO: await _savePersonalData(); - Solo actualizar foto, no todos los datos

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('Foto de perfil actualizada correctamente'),
            backgroundColor: ConstantColors.colorSuccess,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
          ),
        );
      }
    } catch (e, stackTrace) {
      print('❌ [PersonalData] Error al subir imagen: $e');
      print('❌ [PersonalData] Stack trace: $stackTrace');

      setState(() => _isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error al subir imagen: $e'),
            backgroundColor: ConstantColors.colorError,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
          ),
        );
      }
    }
  }

  Future<void> _savePersonalData() async {
    try {
      print('🔍 [PersonalData] Iniciando guardado de datos personales...');

      // Validar el formulario antes de guardar - CORREGIR NULL CHECK
      if (_formKey.currentState?.validate() == true) {
        final data = {
          'name': _nameController.text.trim(),
          'email': _emailController.text.trim(),
          'phone_number': _phoneController.text.trim(),
          'address': _addressController.text.trim(),
        };

        print('🔍 [PersonalData] Datos a guardar: $data');
        await _personalDataService.updatePersonalData(data);
        print('✅ [PersonalData] Datos guardados exitosamente');

        _hasChanges = true; // Marcar que hubo cambios

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: const Text('Datos guardados correctamente'),
              backgroundColor: ConstantColors.colorSuccess,
              behavior: SnackBarBehavior.floating,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
          );
        }
      } else {
        print('❌ [PersonalData] Validación del formulario falló');
      }
    } catch (e, stackTrace) {
      print('❌ [PersonalData] Error al guardar datos: $e');
      print('❌ [PersonalData] Stack trace: $stackTrace');

      setState(() => _isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error al guardar datos: $e'),
            backgroundColor: ConstantColors.colorError,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
          ),
        );
      }
    }
  }

  void _handleSave() async {
    if (_isEditing) {
      setState(() => _isLoading = true);

      // Validar y guardar el formulario - CORREGIR NULL CHECK
      if (_formKey.currentState?.validate() == true) {
        _formKey.currentState?.save();
        await _savePersonalData();

        setState(() {
          _isLoading = false;
          _isEditing = false;
        });
      } else {
        // Si la validación falla, mostrar error pero mantener en modo edición
        setState(() => _isLoading = false);
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: const Text(
                'Por favor corrige los errores en el formulario',
              ),
              backgroundColor: ConstantColors.colorError,
              behavior: SnackBarBehavior.floating,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
          );
        }
      }
    } else {
      setState(() => _isEditing = true);
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _addressController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false, // Controlar manualmente el pop
      onPopInvoked: (didPop) {
        if (!didPop) {
          // Retornar el valor de cambios cuando se cierre la pantalla
          print(
            '🔍 [PersonalData] Pantalla cerrándose con cambios: $_hasChanges',
          );
          Navigator.of(context).pop(_hasChanges);
        }
      },
      child: Scaffold(
        backgroundColor: Colors.white,
        appBar: AppBar(
          title: Text(
            'Mi Perfil',
            style: TextStyle(
              fontWeight: FontWeight.w700,
              fontSize: 20,
              color: ConstantColors.colorAppBarText,
              letterSpacing: 0.5,
            ),
          ),
          backgroundColor: ConstantColors.colorAppBarBackground,
          elevation: 0,
          centerTitle: true,
          iconTheme: IconThemeData(color: ConstantColors.colorAppBarIcon),
          leading: IconButton(
            icon: Icon(Icons.arrow_back, color: ConstantColors.colorAppBarIcon),
            onPressed: () {
              print(
                '🔍 [PersonalData] Botón back presionado con cambios: $_hasChanges',
              );
              Navigator.of(context).pop(_hasChanges);
            },
          ),
          shape: const RoundedRectangleBorder(
            borderRadius: BorderRadius.vertical(bottom: Radius.circular(20)),
          ),
          actions: [
            IconButton(
              icon: Icon(
                _isEditing ? Icons.save_rounded : Icons.edit_rounded,
                size: 24,
              ),
              onPressed: _isLoading ? null : _handleSave,
            ),
          ],
        ),
        body: Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [ConstantColors.colorPageBackground, Colors.white],
            ),
          ),
          child: _isLoading
              ? const Center(child: CircularProgressIndicator())
              : SingleChildScrollView(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 24,
                    vertical: 32,
                  ),
                  child: FadeTransition(
                    opacity: _fadeAnimation,
                    child: SlideTransition(
                      position: _slideAnimation,
                      child: Column(
                        children: [
                          // Header con foto de perfil
                          _buildProfileHeader(),
                          const SizedBox(height: 32),

                          // Información personal - ENVUELTO EN FORM
                          Form(key: _formKey, child: _buildPersonalInfoCard()),
                        ],
                      ),
                    ),
                  ),
                ),
        ), // Cierre del Scaffold
      ), // Cierre del PopScope
    );
  }

  Widget _buildProfileHeader() {
    return Container(
      decoration: BoxDecoration(
        color: ConstantColors.colorCardBackground,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: ConstantColors.colorCardShadow.withAlpha(30),
            blurRadius: 20,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      padding: const EdgeInsets.all(24),
      child: Column(
        children: [
          Stack(
            alignment: Alignment.center,
            children: [
              Container(
                width: 120,
                height: 120,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  border: Border.all(
                    color: ConstantColors.colorImageBorder,
                    width: 3,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: ConstantColors.colorImageShadow.withAlpha(40),
                      blurRadius: 12,
                      spreadRadius: 2,
                    ),
                  ],
                ),
                child: ClipOval(
                  child:
                      _profilePhotoPath != null && _profilePhotoPath!.isNotEmpty
                      ? Image.network(
                          _profilePhotoPath!,
                          fit: BoxFit.cover,
                          errorBuilder: (context, error, stackTrace) {
                            print('❌ Error cargando imagen: $error');
                            return _buildDefaultAvatar();
                          },
                          loadingBuilder: (context, child, loadingProgress) {
                            if (loadingProgress == null) return child;
                            return Container(
                              color: ConstantColors.darkGrey.withOpacity(0.1),
                              child: Center(
                                child: CircularProgressIndicator(
                                  value:
                                      loadingProgress.expectedTotalBytes != null
                                      ? loadingProgress.cumulativeBytesLoaded /
                                            loadingProgress.expectedTotalBytes!
                                      : null,
                                  color: ConstantColors.colorButtonBackground,
                                ),
                              ),
                            );
                          },
                        )
                      : _buildDefaultAvatar(),
                ),
              ),
              if (_isEditing)
                Positioned(
                  bottom: 0,
                  right: 0,
                  child: GestureDetector(
                    onTap: _pickAndUploadImage,
                    child: Container(
                      width: 44,
                      height: 44,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: ConstantColors.colorButtonBackground,
                        border: Border.all(color: Colors.white, width: 3),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.2),
                            blurRadius: 8,
                            offset: const Offset(0, 2),
                          ),
                        ],
                      ),
                      child: Icon(
                        Icons.camera_alt_rounded,
                        size: 20,
                        color: ConstantColors.colorButtonText,
                      ),
                    ),
                  ),
                ),
            ],
          ),
          const SizedBox(height: 20),
          Text(
            _nameController.text.isNotEmpty
                ? _nameController.text
                : 'Sin nombre',
            style: TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.w700,
              color: ConstantColors.colorCardTitle,
              height: 1.2,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 4),
          Text(
            _emailController.text.isNotEmpty
                ? _emailController.text
                : 'Sin email',
            style: TextStyle(
              fontSize: 16,
              color: ConstantColors.colorCardText,
              fontWeight: FontWeight.w400,
            ),
            textAlign: TextAlign.center,
          ),
          if (_isEditing) ...[
            const SizedBox(height: 16),
            Text(
              'Toca la cámara para cambiar tu foto',
              style: TextStyle(
                fontSize: 12,
                color: ConstantColors.colorCardText.withOpacity(0.7),
                fontStyle: FontStyle.italic,
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildDefaultAvatar() {
    return Container(
      color: ConstantColors.darkGrey.withOpacity(0.1),
      child: Center(
        child: Icon(
          Icons.person_rounded,
          size: 50,
          color: ConstantColors.colorCardText.withOpacity(0.5),
        ),
      ),
    );
  }

  Widget _buildPersonalInfoCard() {
    return Container(
      decoration: BoxDecoration(
        color: ConstantColors.colorCardBackground,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: ConstantColors.colorCardShadow.withAlpha(30),
            blurRadius: 20,
            offset: const Offset(0, 4),
          ),
        ],
        border: Border.all(
          color: ConstantColors.colorCardBorder.withOpacity(0.1),
          width: 1,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header de la tarjeta
          Padding(
            padding: const EdgeInsets.all(24),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: ConstantColors.colorButtonBackground.withOpacity(
                      0.1,
                    ),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(
                    Icons.person_outline_rounded,
                    size: 22,
                    color: ConstantColors.colorButtonBackground,
                  ),
                ),
                const SizedBox(width: 12),
                Text(
                  'Información Personal',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                    color: ConstantColors.colorCardTitle,
                  ),
                ),
              ],
            ),
          ),

          // Campos de información
          _buildInfoField(
            label: 'Nombre Completo',
            icon: Icons.person_rounded,
            controller: _nameController,
            isEditing: _isEditing,
            validator: _required,
          ),
          _buildDivider(),
          _buildInfoField(
            label: 'Correo Electrónico',
            icon: Icons.email_rounded,
            controller: _emailController,
            isEditing: _isEditing,
            keyboardType: TextInputType.emailAddress,
            validator: _validateEmail,
          ),
          _buildDivider(),
          _buildInfoField(
            label: 'Teléfono',
            icon: Icons.phone_rounded,
            controller: _phoneController,
            isEditing: _isEditing,
            keyboardType: TextInputType.phone,
            validator: _required,
          ),
          _buildDivider(),
          _buildInfoField(
            label: 'Dirección',
            icon: Icons.location_on_rounded,
            controller: _addressController,
            isEditing: _isEditing,
            maxLines: 2,
          ),
        ],
      ),
    );
  }

  Widget _buildInfoField({
    required String label,
    required IconData icon,
    required TextEditingController controller,
    required bool isEditing,
    TextInputType? keyboardType,
    FormFieldValidator<String>? validator,
    int maxLines = 1,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(
            icon,
            size: 20,
            color: ConstantColors.colorCardText.withOpacity(0.7),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: TextStyle(
                    fontSize: 12,
                    color: ConstantColors.colorCardText.withOpacity(0.7),
                    fontWeight: FontWeight.w500,
                  ),
                ),
                const SizedBox(height: 6),
                if (isEditing)
                  TextFormField(
                    controller: controller,
                    keyboardType: keyboardType,
                    maxLines: maxLines,
                    style: TextStyle(
                      fontSize: 16,
                      color: ConstantColors.colorCardText,
                      fontWeight: FontWeight.w500,
                    ),
                    decoration: InputDecoration(
                      isDense: true,
                      contentPadding: const EdgeInsets.symmetric(
                        vertical: 8,
                        horizontal: 0,
                      ),
                      border: InputBorder.none,
                      enabledBorder: InputBorder.none,
                      focusedBorder: InputBorder.none,
                      errorBorder: InputBorder.none,
                      focusedErrorBorder: InputBorder.none,
                      errorStyle: TextStyle(
                        fontSize: 12,
                        color: ConstantColors.colorError,
                      ),
                    ),
                    validator: validator,
                  )
                else
                  Text(
                    controller.text.isNotEmpty
                        ? controller.text
                        : 'No especificado',
                    style: TextStyle(
                      fontSize: 16,
                      color: controller.text.isNotEmpty
                          ? ConstantColors.colorCardText
                          : ConstantColors.colorCardText.withOpacity(0.5),
                      fontWeight: FontWeight.w500,
                    ),
                    maxLines: maxLines,
                    overflow: TextOverflow.ellipsis,
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDivider() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Divider(
        height: 1,
        thickness: 1,
        color: ConstantColors.colorCardBorder.withOpacity(0.2),
      ),
    );
  }

  String? _required(String? value) => (value == null || value.trim().isEmpty)
      ? 'Este campo es obligatorio'
      : null;

  String? _validateEmail(String? value) {
    if (value == null || value.trim().isEmpty)
      return 'Por favor ingresa tu email';
    if (!RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(value))
      return 'Ingresa un email válido';
    return null;
  }
}
