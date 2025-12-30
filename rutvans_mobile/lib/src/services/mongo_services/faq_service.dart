import 'package:mongo_dart/mongo_dart.dart';
import 'package:rutvans_mobile/src/screens/profile/help_center/faq_item_model.dart';

class FaqService {
  static const String _connectionString = 'mongodb+srv://valenciaalmeidavictor:Valencia123%4012@consultoria.2j6jkko.mongodb.net/dbrutvans?retryWrites=true&w=majority';
  Db? _db;

  Future<List<FAQItem>> fetchFAQs() async {
    try {
      _db = await Db.create(_connectionString);
      await _db?.open();

      final collection = _db!.collection('faqs');
      final faqDocs = await collection.find().toList();

      final faqs = faqDocs.map((doc) => FAQItem(
        question: doc['question']?.toString() ?? 'Pregunta no disponible',
        answer: doc['answer']?.toString() ?? 'Respuesta no disponible',
        isExpanded: doc['isExpanded'] as bool? ?? false,
      )).toList();

      return faqs;
    } catch (e) {
      throw Exception('Error al cargar preguntas frecuentes: $e');
    } finally {
      await _db?.close();
    }
  }
}