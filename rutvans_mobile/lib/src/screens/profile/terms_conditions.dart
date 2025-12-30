import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

class TermsConditions extends StatefulWidget {
  const TermsConditions({super.key});

  @override
  State<TermsConditions> createState() => _TermsConditionsState();
}

class _TermsConditionsState extends State<TermsConditions> 
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _fadeAnimation;
  late Animation<Offset> _slideAnimation;
  bool _acceptedTerms = false;

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

  Future<void> _launchUrl(String url) async {
    if (!await launchUrl(Uri.parse(url))) {
      throw Exception('Could not launch $url');
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
          'Términos y Condiciones',
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
            icon: const Icon(Icons.file_download),
            onPressed: () {
              // Exportar como PDF
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text('Descargando términos y condiciones en PDF'),
                  backgroundColor: Colors.green,
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
                      // Encabezado
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: const Color.fromRGBO(245, 124, 0, 0.1),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: const Color.fromRGBO(245, 124, 0, 0.3),
                          ),
                        ),
                        child: Row(
                          children: [
                            Icon(Icons.gavel, color: primaryColor, size: 28),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Text(
                                'Última actualización: ${_getCurrentDate()}',
                                style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w500,
                                  color: primaryColor,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),

                      // Tarjeta de contenido
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
                            // Sección 1
                            _buildSectionTitle(
                              '1. Aceptación de los Términos',
                              primaryColor,
                            ),
                            const SizedBox(height: 8),
                            const Text(
                              'Al utilizar nuestra aplicación, usted acepta cumplir con estos términos y condiciones. '
                              'Si no está de acuerdo con alguno de estos términos, no debe utilizar nuestros servicios.',
                              style: TextStyle(fontSize: 13, color: textColor),
                            ),
                            const SizedBox(height: 16),

                            // Sección 2
                            _buildSectionTitle(
                              '2. Uso del Servicio',
                              primaryColor,
                            ),
                            const SizedBox(height: 8),
                            const Text(
                              'Nuestra aplicación está diseñada para facilitar la movilidad urbana. Usted se compromete a:',
                              style: TextStyle(fontSize: 13, color: textColor),
                            ),
                            const SizedBox(height: 8),
                            _buildBulletPoint(
                              'Utilizar el servicio solo para fines legales',
                            ),
                            _buildBulletPoint(
                              'No interferir con el funcionamiento de la aplicación',
                            ),
                            _buildBulletPoint(
                              'Proporcionar información veraz y actualizada',
                            ),
                            const SizedBox(height: 16),

                            // Sección 3
                            _buildSectionTitle(
                              '3. Privacidad y Datos',
                              primaryColor,
                            ),
                            const SizedBox(height: 8),
                            const Text(
                              'Respetamos su privacidad. Todos los datos personales se manejan de acuerdo con nuestra ',
                              style: TextStyle(fontSize: 13, color: textColor),
                            ),
                            GestureDetector(
                              onTap: () => _launchUrl('https://tudominio.com/privacidad'),
                              child: Text(
                                'Política de Privacidad',
                                style: TextStyle(
                                  fontSize: 13,
                                  color: primaryColor,
                                  decoration: TextDecoration.underline,
                                ),
                              ),
                            ),
                            const SizedBox(height: 16),

                            // Sección 4
                            _buildSectionTitle(
                              '4. Conducta del Usuario',
                              primaryColor,
                            ),
                            const SizedBox(height: 8),
                            const Text(
                              'Está prohibido cualquier uso fraudulento, abusivo o que viole los derechos de otros usuarios. '
                              'Nos reservamos el derecho de suspender cuentas que infrinjan estas normas.',
                              style: TextStyle(fontSize: 13, color: textColor),
                            ),
                            const SizedBox(height: 16),

                            // Sección 5
                            _buildSectionTitle(
                              '5. Modificaciones',
                              primaryColor,
                            ),
                            const SizedBox(height: 8),
                            const Text(
                              'Podemos modificar estos términos en cualquier momento. Las versiones actualizadas estarán disponibles '
                              'en la aplicación y entrarán en vigor inmediatamente después de su publicación.',
                              style: TextStyle(fontSize: 13, color: textColor),
                            ),
                            const SizedBox(height: 24),

                            // Checkbox de aceptación
                            if (ModalRoute.of(context)?.isFirst != true)
                              Row(
                                children: [
                                  Checkbox(
                                    value: _acceptedTerms,
                                    onChanged: (value) {
                                      setState(() {
                                        _acceptedTerms = value ?? false;
                                      });
                                    },
                                    activeColor: primaryColor,
                                  ),
                                  Expanded(
                                    child: RichText(
                                      text: TextSpan(
                                        style: const TextStyle(
                                          fontSize: 12,
                                          color: textColor,
                                        ),
                                        children: [
                                          const TextSpan(
                                            text: 'He leído y acepto los ',
                                          ),
                                          TextSpan(
                                            text: 'Términos y Condiciones',
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
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),

                      // Enlaces legales
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
                          children: [
                            _buildLegalLink(
                              'Política de Privacidad',
                              Icons.privacy_tip_outlined,
                              primaryColor,
                              () => _launchUrl('https://tudominio.com/privacidad'),
                            ),
                            const Divider(height: 24),
                            _buildLegalLink(
                              'Contrato de Usuario',
                              Icons.assignment_outlined,
                              primaryColor,
                              () => _launchUrl('https://tudominio.com/contrato'),
                            ),
                            const Divider(height: 24),
                            _buildLegalLink(
                              'Preguntas Frecuentes',
                              Icons.help_outline,
                              primaryColor,
                              () => Navigator.pushNamed(context, '/centro-ayuda'),
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

          // Botón de aceptar (solo visible si no es la primera pantalla)
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
              child: SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: primaryColor,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  onPressed: _acceptedTerms
                      ? () {
                          Navigator.pop(context, true);
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(
                              content: Text('Términos y condiciones aceptados'),
                              backgroundColor: Colors.green,
                            ),
                          );
                        }
                      : null,
                  child: const Text(
                    'ACEPTAR TÉRMINOS',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title, Color color) {
    return Text(
      title,
      style: TextStyle(
        fontSize: 15,
        fontWeight: FontWeight.bold,
        color: color,
      ),
    );
  }

  Widget _buildBulletPoint(String text) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('• ', style: TextStyle(fontSize: 16)),
          Expanded(
            child: Text(
              text,
              style: const TextStyle(fontSize: 13, color: Color(0xFF424242)),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLegalLink(
      String text, IconData icon, Color color, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(8),
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 8),
        child: Row(
          children: [
            Icon(icon, color: color),
            const SizedBox(width: 12),
            Text(
              text,
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w500,
                color: color,
              ),
            ),
            const Spacer(),
            Icon(Icons.chevron_right, color: color),
          ],
        ),
      ),
    );
  }

  String _getCurrentDate() {
    final now = DateTime.now();
    return '${now.day}/${now.month}/${now.year}';
  }
}