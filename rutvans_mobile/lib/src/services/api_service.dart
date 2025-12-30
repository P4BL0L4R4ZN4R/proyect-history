import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static const String baseUrl = 'http://127.0.0.1:8000/api';

  // LOGIN
  static Future<Map<String, dynamic>?> login(
    String email,
    String password,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/mobile-login'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'ngrok-skip-browser-warning': 'true',
        },
        body: jsonEncode({'email': email, 'password': password}),
      );

      print('🔍 [ApiService] Login Status Code: ${response.statusCode}');
      print('🔍 [ApiService] Login Response: ${response.body}');

      if (response.statusCode == 200) {
        final json = jsonDecode(response.body);

        // Guardar user_id, user_name y auth_token en SharedPreferences
        final prefs = await SharedPreferences.getInstance();

        // El backend devuelve { token: "...", user: { id: 1, name: "...", ... } }
        final user = json['user'];

        // Guardar ID
        if (user != null &&
            user['id'] != null &&
            (user['id'] is int ||
                int.tryParse(user['id'].toString()) != null)) {
          await prefs.setString('user_id', user['id'].toString());
        } else {
          print('❌ El id recibido no es válido: ${user?['id']}');
        }

        // Guardar nombre
        await prefs.setString('user_name', user?['name'] ?? 'Usuario');

        // Guardar token si existe
        if (json['token'] != null) {
          await prefs.setString('auth_token', json['token']);
          print('✅ [ApiService] Token guardado: ${json['token']}');
        } else {
          print('❌ No se recibió auth_token en la respuesta');
        }

        print(
          '✅ [ApiService] Guardando user_id: ${user?['id']}, user_name: ${user?['name']}',
        );
        return json;
      } else {
        print('❌ [ApiService] Login fallido: ${response.body}');
        return null;
      }
    } catch (e) {
      print('❌ [ApiService] Error al hacer login: $e');
      return null;
    }
  }

  // REGISTER
  static Future<Map<String, dynamic>?> register(
    String name,
    String email,
    String password,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/mobile-register'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'ngrok-skip-browser-warning': 'true',
        },
        body: jsonEncode({'name': name, 'email': email, 'password': password}),
      );

      print('🔍 [ApiService] Register Status Code: ${response.statusCode}');
      print('🔍 [ApiService] Register Response: ${response.body}');

      if (response.statusCode == 201) {
        final json = jsonDecode(response.body);

        // Guardar user_id, user_name y auth_token en SharedPreferences
        final prefs = await SharedPreferences.getInstance();

        // El backend devuelve { token: "...", user: { id: 1, name: "...", ... } }
        final user = json['user'];

        // Guardar ID
        if (user != null &&
            user['id'] != null &&
            (user['id'] is int ||
                int.tryParse(user['id'].toString()) != null)) {
          await prefs.setString('user_id', user['id'].toString());
        } else {
          print('❌ El id recibido no es válido: ${user?['id']}');
        }

        // Guardar nombre
        await prefs.setString('user_name', user?['name'] ?? 'Usuario');

        // Guardar token si existe
        if (json['token'] != null) {
          await prefs.setString('auth_token', json['token']);
          print('✅ [ApiService] Token guardado: ${json['token']}');
        } else {
          print('❌ No se recibió auth_token en la respuesta');
        }

        print(
          '✅ [ApiService] Guardando user_id: ${user?['id']}, user_name: ${user?['name']}',
        );
        return json;
      } else {
        print('❌ [ApiService] Registro fallido: ${response.body}');
        return null;
      }
    } catch (e) {
      print('❌ [ApiService] Error al registrar usuario: $e');
      return null;
    }
  }

  static Future<bool> logout() async {
    print('🔄 [ApiService] Intentando cerrar sesión...');

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      if (token == null || token.isEmpty) {
        print('⚠️ [ApiService] No hay token guardado en SharedPreferences.');
        return false;
      }

      print('🔑 [ApiService] Token encontrado: $token');

      final url = Uri.parse('$baseUrl/logout');
      print('🌐 [ApiService] URL de logout: $url');

      final response = await http.post(
        url,
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'ngrok-skip-browser-warning': 'true',
        },
      );

      print('🔍 [ApiService] Logout Status Code: ${response.statusCode}');
      print('🔍 [ApiService] Logout Response Body: ${response.body}');

      if (response.statusCode == 200) {
        print('✅ [ApiService] Logout exitoso, eliminando datos locales...');
        await prefs.remove('auth_token');
        await prefs.remove('user_id');
        await prefs.remove('user_name');
        return true;
      } else if (response.statusCode == 401) {
        print(
          '⚠️ [ApiService] Usuario no autenticado (401). Limpiando sesión local de todas formas.',
        );
        await prefs.remove('auth_token');
        await prefs.remove('user_id');
        await prefs.remove('user_name');
        return false;
      } else {
        print(
          '❌ [ApiService] Logout fallido. Código: ${response.statusCode}, Body: ${response.body}',
        );
        return false;
      }
    } catch (e, stackTrace) {
      print('❌ [ApiService] Error al cerrar sesión: $e');
      print('🧵 StackTrace: $stackTrace');
      return false;
    }
  }
}
