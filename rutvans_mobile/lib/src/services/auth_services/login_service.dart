// login_service.dart
import 'package:rutvans_mobile/src/services/api_service.dart';

class LoginService {
  Future<Map<String, dynamic>?> login(String email, String password) async {
    return await ApiService.login(email, password);
  }
}
