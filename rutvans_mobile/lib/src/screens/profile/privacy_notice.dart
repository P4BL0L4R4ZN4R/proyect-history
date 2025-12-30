import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

class PrivacyNotice extends StatefulWidget {
  const PrivacyNotice({super.key});

  @override
  State<PrivacyNotice> createState() => _PrivacyNoticeState();
}

class _PrivacyNoticeState extends State<PrivacyNotice> 
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _fadeAnimation;
  late Animation<Offset> _slideAnimation;
  bool _acceptedPrivacy = false;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: const Duration(milliseconds: 600),
      vsync: this,
    );

    _fadeAnimation = Tween<double>(begin: 0, end: 1).animate(
      CurvedAnimation(
        parent: _controller,
        curve: Curves.easeOut,
      ),
    );

    _slideAnimation = Tween<Offset>(
      begin: const Offset(0, 0.2),
      end: Offset.zero,
    ).animate(
      CurvedAnimation(
        parent: _controller,
        curve: Curves.easeOutCubic,
      ),
    );

    _controller.forward();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Future<void> _launchEmail() async {
    final Uri emailLaunchUri = Uri(
      scheme: 'mailto',
      path: 'privacidad@rutvans.com',
      queryParameters: {
        'subject': 'Consulta sobre Aviso de Privacidad',
      },
    );

    if (!await launchUrl(emailLaunchUri)) {
      throw Exception('Could not launch $emailLaunchUri');
    }
  }

  @override
  Widget build(BuildContext context) {
    const primaryColor = Color(0xFFF57C00);
    const backgroundColor = Color(0xFFFFF3E0);
    const cardColor = Colors.white;
    const textColor = Color(0xFF424242);

    return Scaffold(
      backgroundColor: backgroundColor,
      appBar: AppBar(
        title: const Text(
          'Aviso de Privacidad',
          style: TextStyle(
            fontWeight: FontWeight.w600,
            fontSize: 20,
            color: Colors.white,
          ),
        ),
        backgroundColor: primaryColor,
        elevation: 0,
        centerTitle: true,
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          IconButton(
            icon: const Icon(Icons.share),
            onPressed: () {
              // Lógica para compartir
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text('Compartiendo Aviso de Privacidad'),
                ),
              );
            },
          ),
        ],
      ),
      body: Column(
        children: [
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 24),
              child: FadeTransition(
                opacity: _fadeAnimation,
                child: SlideTransition(
                  position: _slideAnimation,
                  child: Column(
                    children: [
                      // Encabezado con información importante
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: const Color.fromRGBO(245, 124, 0, 0.1),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: const Color.fromRGBO(245, 124, 0, 0.3),
                          ),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Icon(Icons.security, color: primaryColor),
                                const SizedBox(width: 8),
                                Text(
                                  'Protección de Datos Personales',
                                  style: TextStyle(
                                    fontWeight: FontWeight.bold,
                                    color: primaryColor,
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 8),
                            const Text(
                              'En cumplimiento con la Ley Federal de Protección de Datos Personales en Posesión de los Particulares.',
                              style: TextStyle(fontSize: 12, color: textColor),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),

                      // Contenido principal
                      Container(
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(
                          color: cardColor,
                          borderRadius: BorderRadius.circular(16),
                          boxShadow: [
                            BoxShadow(
                              color: const Color.fromRGBO(245, 124, 0, 0.1),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            ),
                          ],
                          border: Border.all(
                            color: const Color.fromRGBO(255, 167, 38, 0.5),
                            width: 1.5,
                          ),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            _buildPrivacySection(
                              'Responsable de tus datos',
                              'Rutvans S.A. de C.V., con domicilio en Av. Insurgentes Sur 1234, Col. Condesa, Ciudad de México, C.P. 06140.',
                            ),
                            _buildDivider(),
                            _buildPrivacySection(
                              'Finalidad del tratamiento',
                              'Los datos personales que recabamos se utilizarán para:\n\n'
                              '• Proporcionar los servicios de transporte solicitados\n'
                              '• Facturación y procesos de pago\n'
                              '• Comunicación sobre el servicio\n'
                              '• Mejora de nuestros servicios\n'
                              '• Cumplimiento de obligaciones legales',
                            ),
                            _buildDivider(),
                            _buildPrivacySection(
                              'Datos recabados',
                              'Podemos recabar:\n\n'
                              '• Información de contacto (nombre, email, teléfono)\n'
                              '• Datos de localización\n'
                              '• Datos de pago\n'
                              '• Información de viajes\n'
                              '• Datos de dispositivo',
                            ),
                            _buildDivider(),
                            _buildPrivacySection(
                              'Tus derechos ARCO',
                              'Tienes derecho a:\n\n'
                              '• Acceder a tus datos personales\n'
                              '• Rectificarlos si son inexactos\n'
                              '• Cancelar su uso\n'
                              '• Oponerte al tratamiento\n\n'
                              'Para ejercer estos derechos, contacta a nuestro Departamento de Privacidad.',
                            ),
                            _buildDivider(),
                            _buildPrivacySection(
                              'Transferencia de datos',
                              'Tus datos podrán ser compartidos con:\n\n'
                              '• Autoridades competentes cuando lo requieran\n'
                              '• Proveedores de servicios necesarios para la operación\n'
                              '• Socios comerciales para ofrecerte beneficios',
                            ),
                            _buildDivider(),
                            _buildPrivacySection(
                              'Cambios al aviso',
                              'Nos reservamos el derecho de modificar este aviso. Los cambios se notificarán a través de nuestra aplicación o por correo electrónico.',
                            ),
                            const SizedBox(height: 16),
                            
                            // Contacto
                            Container(
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                color: backgroundColor,
                                borderRadius: BorderRadius.circular(8),
                                border: Border.all(
                                  color: const Color.fromRGBO(245, 124, 0, 0.3),
                                ),
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    'Contacto para privacidad',
                                    style: TextStyle(
                                      fontWeight: FontWeight.bold,
                                      color: primaryColor,
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  const Text(
                                    'Si tienes preguntas sobre este aviso o deseas ejercer tus derechos ARCO:',
                                    style: TextStyle(fontSize: 12),
                                  ),
                                  const SizedBox(height: 8),
                                  InkWell(
                                    onTap: _launchEmail,
                                    child: Text(
                                      'privacidad@rutvans.com',
                                      style: TextStyle(
                                        color: primaryColor,
                                        decoration: TextDecoration.underline,
                                      ),
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  const Text(
                                    'Teléfono: 55 1234 5678',
                                    style: TextStyle(fontSize: 12),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),

                      // Versión simplificada
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: cardColor,
                          borderRadius: BorderRadius.circular(16),
                          boxShadow: [
                            BoxShadow(
                              color: const Color.fromRGBO(245, 124, 0, 0.1),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            ),
                          ],
                          border: Border.all(
                            color: const Color.fromRGBO(255, 167, 38, 0.5),
                            width: 1.5,
                          ),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Icon(Icons.summarize, color: primaryColor),
                                const SizedBox(width: 8),
                                Text(
                                  'Versión simplificada',
                                  style: TextStyle(
                                    fontWeight: FontWeight.bold,
                                    color: primaryColor,
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 12),
                            const Text(
                              'En Rutvans protegemos tus datos personales:\n\n'
                              '• Solo pedimos lo necesario para brindarte el servicio\n'
                              '• Tus datos están seguros con nosotros\n'
                              '• No compartimos tu información sin tu consentimiento\n'
                              '• Puedes solicitar acceso, corrección o eliminación de tus datos en cualquier momento',
                              style: TextStyle(fontSize: 13),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),

          // Botón de aceptación (solo visible si no es la primera pantalla)
          if (ModalRoute.of(context)?.isFirst != true)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: BoxDecoration(
                color: cardColor,
                border: Border(
                  top: BorderSide(
                    color: const Color.fromRGBO(245, 124, 0, 0.2),
                    width: 1,
                  ),
                ),
              ),
              child: Column(
                children: [
                  Row(
                    children: [
                      Checkbox(
                        value: _acceptedPrivacy,
                        onChanged: (value) {
                          setState(() {
                            _acceptedPrivacy = value ?? false;
                          });
                        },
                        activeColor: primaryColor,
                      ),
                      Expanded(
                        child: RichText(
                          text: TextSpan(
                            style: const TextStyle(
                              fontSize: 12,
                              color: Color(0xFF424242),
                            ),
                            children: [
                              const TextSpan(text: 'He leído y acepto el '),
                              TextSpan(
                                text: 'Aviso de Privacidad',
                                style: TextStyle(
                                  color: primaryColor,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: primaryColor,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      onPressed: _acceptedPrivacy
                          ? () {
                              Navigator.pop(context, true);
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  content: Text('Aviso de privacidad aceptado'),
                                  backgroundColor: Colors.green,
                                ),
                              );
                            }
                          : null,
                      child: const Text(
                        'ACEPTAR AVISO DE PRIVACIDAD',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildPrivacySection(String title, String content) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          title,
          style: const TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 8),
        Text(
          content,
          style: const TextStyle(fontSize: 13),
        ),
        const SizedBox(height: 12),
      ],
    );
  }

  Widget _buildDivider() {
    return const Divider(
      height: 25,
      thickness: 1,
      color: Color.fromRGBO(245, 124, 0, 0.2),
    );
  }
}