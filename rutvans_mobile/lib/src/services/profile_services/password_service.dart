import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:rutvans_mobile/src/services/api_service.dart';

class PasswordService {
  // Verificar contraseña actual
  Future<bool> verifyCurrentPassword(String currentPassword) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final userId = prefs.getString('user_id');

      if (userId == null) {
        throw Exception('No hay usuario logeado');
      }

      final url = '${ApiService.baseUrl}/client/verify-password';
      print('🔍 [PasswordService.verifyCurrentPassword] URL: $url');

      final response = await http.post(
        Uri.parse(url),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'ngrok-skip-browser-warning': 'true',
        },
        body: jsonEncode({
          'user_id': userId,
          'current_password': currentPassword,
        }),
      );

      print(
        '🔍 [PasswordService] Verify Password Status: ${response.statusCode}',
      );
      print('🔍 [PasswordService] Verify Password Response: ${response.body}');

      if (response.statusCode == 200) {
        final json = jsonDecode(response.body);
        return json['valid'] ?? false;
      } else {
        throw Exception('Error al verificar contraseña: ${response.body}');
      }
    } catch (e) {
      print('❌ [PasswordService] Error verifying password: $e');
      throw e;
    }
  }

  // Actualizar contraseña (método principal)
  Future<void> updatePassword(String newPassword) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final userId = prefs.getString('user_id');

      if (userId == null) {
        throw Exception('No hay usuario logeado');
      }

      final url = '${ApiService.baseUrl}/client/change-password';
      print('🔍 [PasswordService.updatePassword] URL: $url');

      final response = await http.post(
        Uri.parse(url),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'ngrok-skip-browser-warning': 'true',
        },
        body: jsonEncode({'user_id': userId, 'new_password': newPassword}),
      );

      print(
        '🔍 [PasswordService] Change Password Status: ${response.statusCode}',
      );
      print('🔍 [PasswordService] Change Password Response: ${response.body}');

      if (response.statusCode == 200) {
        return;
      } else {
        throw Exception('Error al cambiar contraseña: ${response.body}');
      }
    } catch (e) {
      print('❌ [PasswordService] Error changing password: $e');
      throw e;
    }
  }

  // Método completo que verifica y actualiza en una sola llamada
  Future<void> updatePasswordWithVerification(
    String currentPassword,
    String newPassword,
  ) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final userId = prefs.getString('user_id');

      if (userId == null) {
        throw Exception('No hay usuario logeado');
      }

      final url = '${ApiService.baseUrl}/client/update-password';
      print('🔍 [PasswordService.updatePasswordWithVerification] URL: $url');

      final response = await http.post(
        Uri.parse(url),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'ngrok-skip-browser-warning': 'true',
        },
        body: jsonEncode({
          'user_id': userId,
          'current_password': currentPassword,
          'new_password': newPassword,
        }),
      );

      print(
        '🔍 [PasswordService] Update Password Status: ${response.statusCode}',
      );
      print('🔍 [PasswordService] Update Password Response: ${response.body}');

      if (response.statusCode == 200) {
        return;
      } else {
        final error = jsonDecode(response.body);
        throw Exception(error['error'] ?? 'Error al actualizar contraseña');
      }
    } catch (e) {
      print('❌ [PasswordService] Error updating password: $e');
      throw e;
    }
  }
}
