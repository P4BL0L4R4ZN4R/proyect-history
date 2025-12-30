import 'package:rutvans_mobile/src/services/api_service.dart';

class SignupService {
  Future<Map<String, dynamic>?> register(String name, String email, String password) async {
    return await ApiService.register(name, email, password);
  }
}
