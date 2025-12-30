import 'package:flutter/material.dart';
import 'package:rutvans_mobile/src/services/mongo_services/faq_service.dart';
import 'package:rutvans_mobile/src/constants/constant_colors.dart';
import 'package:rutvans_mobile/src/screens/profile/help_center/faq_item_model.dart';
import 'package:rutvans_mobile/src/screens/profile/help_center/widgets/emergency_section.dart';
import 'package:rutvans_mobile/src/screens/profile/help_center/widgets/support_section.dart';
import 'package:rutvans_mobile/src/screens/profile/help_center/widgets/faq_section.dart';

class HelpCenter extends StatefulWidget {
  const HelpCenter({super.key});

  @override
  State<HelpCenter> createState() => _HelpCenterState();
}

class _HelpCenterState extends State<HelpCenter> with SingleTickerProviderStateMixin {
  late final AnimationController _controller;
  late final Animation<double> _fadeAnimation;
  late final Animation<Offset> _slideAnimation;

  List<FAQItem> _faqs = [];
  bool _isLoading = true;
  String? _errorMessage;

  final FaqService _faqService = FaqService();

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: const Duration(milliseconds: 600),
      vsync: this,
    )..forward();

    _fadeAnimation = Tween<double>(begin: 0, end: 1).animate(
      CurvedAnimation(parent: _controller, curve: Curves.easeOut),
    );

    _slideAnimation = Tween<Offset>(
      begin: const Offset(0, 0.2),
      end: Offset.zero,
    ).animate(
      CurvedAnimation(parent: _controller, curve: Curves.easeOutCubic),
    );

    _fetchFAQs();
  }

  Future<void> _fetchFAQs() async {
    try {
      final faqs = await _faqService.fetchFAQs();
      setState(() {
        _faqs = faqs;
        _errorMessage = faqs.isEmpty ? 'No se encontraron preguntas frecuentes' : null;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _errorMessage = 'Error al cargar preguntas frecuentes: $e';
        _isLoading = false;
      });
    }
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
          'Centro de Ayuda',
          style: TextStyle(
            fontWeight: FontWeight.w600,
            fontSize: 20,
            color: ConstantColors.colorAppBarText,
          ),
        ),
        backgroundColor: ConstantColors.colorAppBarBackground,
        elevation: 0,
        centerTitle: true,
        iconTheme: const IconThemeData(color: ConstantColors.colorAppBarIcon),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 24),
        child: FadeTransition(
          opacity: _fadeAnimation,
          child: SlideTransition(
            position: _slideAnimation,
            child: Column(
              children: [
                const EmergencySection(),
                const SizedBox(height: 24),
                const SupportSection(),
                const SizedBox(height: 24),
                FAQSection(faqs: _faqs, isLoading: _isLoading, errorMessage: _errorMessage),
              ],
            ),
          ),
        ),
      ),
    );
  }
}