// ...importaciones
import 'dart:io';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:rutvans_mobile/src/services/payment_services/shipping_service.dart';

const Color primaryOrange = Color(0xFFFF9800);
const Color textGray = Color(0xFF454545);

class ShippingPage extends StatefulWidget {
  const ShippingPage({super.key});

  @override
  State<ShippingPage> createState() => _ShippingPageState();
}

class _ShippingPageState extends State<ShippingPage> {
  final TextEditingController originController = TextEditingController();
  final TextEditingController destinationController = TextEditingController();
  final TextEditingController receiverNameController = TextEditingController();
  final TextEditingController referencesController = TextEditingController();
  final TextEditingController descriptionController = TextEditingController();
  final TextEditingController weightController = TextEditingController();

  String? _selectedSchedule;
  DateTime? _selectedDate;
  String? _selectedSize;
  bool _isFragile = false;
  File? _imageFile;

  final ImagePicker _picker = ImagePicker();

  bool get _isFormComplete =>
      originController.text.trim().isNotEmpty &&
      destinationController.text.trim().isNotEmpty &&
      receiverNameController.text.trim().isNotEmpty &&
      descriptionController.text.trim().isNotEmpty &&
      weightController.text.trim().isNotEmpty &&
      _selectedDate != null &&
      _selectedSchedule != null &&
      _selectedSize != null;

  Future<void> _pickImage() async {
    final XFile? pickedFile = await _picker.pickImage(source: ImageSource.gallery);
    if (pickedFile != null) {
      setState(() {
        _imageFile = File(pickedFile.path);
      });
    }
  }

  void _resetForm() {
    setState(() {
      originController.clear();
      destinationController.clear();
      receiverNameController.clear();
      referencesController.clear();
      descriptionController.clear();
      weightController.clear();
      _selectedDate = null;
      _selectedSchedule = null;
      _selectedSize = null;
      _isFragile = false;
      _imageFile = null;
    });
  }

  Future<void> _submitRequest() async {
    final imageBytes = _imageFile != null ? base64Encode(_imageFile!.readAsBytesSync()) : null;

    final requestData = {
      'origin': originController.text,
      'destination': destinationController.text,
      'receiverName': receiverNameController.text,
      'references': referencesController.text,
      'schedule': _selectedSchedule,
      'date': _selectedDate?.toIso8601String(),
      'description': descriptionController.text,
      'size': _selectedSize,
      'weight': weightController.text,
      'fragile': _isFragile,
      'imageBase64': imageBytes,
      'createdAt': DateTime.now().toIso8601String(),
    };

    final service = ShippingService();
    await service.insertShippingRequest(requestData);

    if (mounted) {
      _showSuccessDialog(context);
    }
  }

  void _showSuccessDialog(BuildContext context) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          backgroundColor: Colors.white,
          title: const Text(
            'Solicitud enviada',
            style: TextStyle(
              fontWeight: FontWeight.bold,
              color: primaryOrange,
            ),
          ),
          content: const Text(
            'Su solicitud ha sido enviada para revisión y está en espera de ser aceptada por un conductor disponible.',
            style: TextStyle(fontSize: 15),
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context, rootNavigator: true).pop();
                _resetForm();
              },
              style: TextButton.styleFrom(
                foregroundColor: Colors.white,
                backgroundColor: primaryOrange,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
              child: const Text('Aceptar'),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Formulario de Envío"),
        backgroundColor: primaryOrange,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Completa los datos para realizar tu envío de manera segura y rápida.',
              style: TextStyle(color: textGray, fontSize: 14, height: 1.4),
            ),
            const SizedBox(height: 24),

            _buildSectionTitle('Datos del envío', Icons.local_shipping),
            _buildTextField(controller: originController, label: 'Lugar de origen'),
            const SizedBox(height: 16),
            _buildTextField(controller: destinationController, label: 'Lugar de destino'),
            const SizedBox(height: 16),

            Row(
              children: [
                Expanded(child: _buildDropdownField()),
                const SizedBox(width: 16),
                Expanded(child: _buildDateField(context)),
              ],
            ),
            const SizedBox(height: 24),

            _buildSectionTitle('Datos del receptor', Icons.person),
            _buildTextField(controller: receiverNameController, label: 'Nombre y apellido'),
            const SizedBox(height: 16),
            _buildTextField(controller: referencesController, label: 'Referencias'),
            const SizedBox(height: 24),

            _buildSectionTitle('Datos del paquete', Icons.inventory),
            _buildTextField(controller: descriptionController, label: 'Descripción general', maxLines: 3),
            const SizedBox(height: 16),

            DropdownButtonFormField<String>(
              value: _selectedSize,
              hint: const Text('Tamaño del paquete'),
              items: ['Pequeño', 'Mediano', 'Grande'].map((String value) {
                return DropdownMenuItem<String>(value: value, child: Text(value));
              }).toList(),
              onChanged: (value) => setState(() => _selectedSize = value),
              decoration: _inputDecoration(),
            ),
            const SizedBox(height: 16),

            _buildTextField(controller: weightController, label: 'Peso (kg)'),
            const SizedBox(height: 16),

            SwitchListTile(
              title: const Text('¿El paquete es frágil?'),
              activeColor: primaryOrange,
              value: _isFragile,
              onChanged: (value) => setState(() => _isFragile = value),
            ),
            const SizedBox(height: 16),

            _buildSectionTitle('Imagen del paquete', Icons.image),
            _imageFile == null
                ? TextButton.icon(
                    onPressed: _pickImage,
                    icon: const Icon(Icons.add_a_photo, color: primaryOrange),
                    label: const Text('Seleccionar imagen'),
                  )
                : Column(
                    children: [
                      Image.file(_imageFile!, height: 150),
                      TextButton.icon(
                        onPressed: _pickImage,
                        icon: const Icon(Icons.edit, color: primaryOrange),
                        label: const Text('Cambiar imagen'),
                      ),
                    ],
                  ),
            const SizedBox(height: 32),

            Center(
              child: SizedBox(
                width: MediaQuery.of(context).size.width * 0.5,
                child: ElevatedButton(
                  onPressed: _isFormComplete ? _submitRequest : null,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: _isFormComplete ? primaryOrange : Colors.grey,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    elevation: 2,
                  ),
                  child: const Text('Aceptar', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  InputDecoration _inputDecoration() => InputDecoration(
        filled: true,
        fillColor: Colors.grey[100],
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: primaryOrange, width: 2)),
      );

  Widget _buildSectionTitle(String title, IconData icon) => Row(
        children: [
          Icon(icon, color: primaryOrange, size: 24),
          const SizedBox(width: 8),
          Text(title, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.black)),
        ],
      );

  Widget _buildTextField({required TextEditingController controller, required String label, int maxLines = 1}) {
    return TextField(
      controller: controller,
      maxLines: maxLines,
      onChanged: (_) => setState(() {}),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(color: textGray),
        filled: true,
        fillColor: Colors.grey[100],
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: primaryOrange, width: 2)),
      ),
    );
  }

  Widget _buildDropdownField() {
    return DropdownButtonFormField<String>(
      value: _selectedSchedule,
      hint: const Text('Horario'),
      items: ['Mañana', 'Tarde', 'Noche'].map((String value) {
        return DropdownMenuItem<String>(value: value, child: Text(value));
      }).toList(),
      onChanged: (value) => setState(() => _selectedSchedule = value),
      decoration: _inputDecoration(),
    );
  }

  Widget _buildDateField(BuildContext context) {
    return TextField(
      readOnly: true,
      decoration: InputDecoration(
        hintText: _selectedDate == null
            ? 'Fecha'
            : '${_selectedDate!.day}/${_selectedDate!.month}/${_selectedDate!.year}',
        filled: true,
        fillColor: Colors.grey[100],
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: primaryOrange, width: 2)),
        suffixIcon: IconButton(
          icon: const Icon(Icons.calendar_today, color: primaryOrange),
          onPressed: () async {
            final picked = await showDatePicker(
              context: context,
              initialDate: DateTime.now(),
              firstDate: DateTime(2025),
              lastDate: DateTime(2030),
              builder: (context, child) => Theme(
                data: Theme.of(context).copyWith(
                  colorScheme: const ColorScheme.light(
                    primary: primaryOrange,
                    onPrimary: Colors.white,
                    onSurface: Colors.black,
                  ),
                ),
                child: child!,
              ),
            );
            if (picked != null && picked != _selectedDate) {
              setState(() => _selectedDate = picked);
            }
          },
        ),
      ),
    );
  }
}
