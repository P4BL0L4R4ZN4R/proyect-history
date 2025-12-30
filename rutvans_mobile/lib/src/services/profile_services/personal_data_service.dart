import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:rutvans_mobile/src/services/api_service.dart';

class PersonalDataService {
  Future<Map<String, dynamic>> fetchPersonalData() async {
    final prefs = await SharedPreferences.getInstance();
    final userId = prefs.getString('user_id');
    if (userId == null) {
      throw Exception('No hay usuario logeado');
    }

    final url = '${ApiService.baseUrl}/client/user?user_id=$userId';
    print('🔍 [PersonalDataService.fetchPersonalData] URL: $url');
    print('🔍 [PersonalDataService.fetchPersonalData] userId: $userId');

    final response = await http.get(
      Uri.parse(url),
      headers: {
        'Content-Type': 'application/json',
        'ngrok-skip-browser-warning': 'true',
      },
    );

    print(
      '🔍 [PersonalDataService.fetchPersonalData] Status Code: ${response.statusCode}',
    );
    print(
      '🔍 [PersonalDataService.fetchPersonalData] Response Body: ${response.body}',
    );

    if (response.statusCode == 200) {
      final json = jsonDecode(response.body);
      print(
        '🔍 [PersonalDataService.fetchPersonalData] Datos parseados: $json',
      );
      return {
        'name': json['name'] ?? '',
        'email': json['email'] ?? '',
        'phone_number': json['phone_number'] ?? '',
        'address': json['address'] ?? '',
        'profile_photo_path': json['profile_photo_path'] ?? '',
      };
    } else {
      print(
        '❌ [PersonalDataService.fetchPersonalData] Error ${response.statusCode}: ${response.body}',
      );
      throw Exception('Error al recuperar datos personales: ${response.body}');
    }
  }

  Future<void> updatePersonalData(Map<String, dynamic> data) async {
    final prefs = await SharedPreferences.getInstance();
    final userId = prefs.getString('user_id');
    if (userId == null) {
      throw Exception('No hay usuario logeado');
    }

    // Filtrar solo los campos no nulos y construir el cuerpo JSON
    final body = <String, dynamic>{};
    if (data['name'] != null) body['name'] = data['name'].toString();
    if (data['email'] != null) body['email'] = data['email'].toString();
    if (data['phone_number'] != null) {
      body['phone_number'] = data['phone_number']
          .toString()
          .trim(); // Asegurar formato
      print('Enviando phone_number: ${body['phone_number']}'); // Depuración
    }
    if (data['address'] != null) body['address'] = data['address'].toString();
    body['user_id'] = userId;

    print('Cuerpo enviado: $body'); // Depuración

    final url = '${ApiService.baseUrl}/client/user';
    print('🔍 [PersonalDataService.updatePersonalData] URL: $url');

    final response = await http.patch(
      Uri.parse(url),
      headers: {
        'Content-Type': 'application/json',
        'ngrok-skip-browser-warning': 'true',
      },
      body: jsonEncode(body),
    );

    print(
      '🔍 [PersonalDataService.updatePersonalData] Status Code: ${response.statusCode}',
    );
    print(
      '🔍 [PersonalDataService.updatePersonalData] Response Body: ${response.body}',
    );

    if (response.statusCode != 200) {
      throw Exception('Error al actualizar datos personales: ${response.body}');
    }
  }

  Future<String> uploadProfilePhoto(File imageFile) async {
    final prefs = await SharedPreferences.getInstance();
    final userId = prefs.getString('user_id');
    if (userId == null) {
      throw Exception('No hay usuario logeado');
    }

    final url = '${ApiService.baseUrl}/client/user/upload-photo';
    print('🔍 [PersonalDataService.uploadProfilePhoto] URL: $url');
    print('🔍 [PersonalDataService.uploadProfilePhoto] userId: $userId');

    final request = http.MultipartRequest('POST', Uri.parse(url));
    request.headers.addAll({'ngrok-skip-browser-warning': 'true'});
    request.fields['user_id'] = userId;
    request.files.add(
      await http.MultipartFile.fromPath('photo', imageFile.path),
    );

    final response = await request.send();
    final responseBody = await http.Response.fromStream(response);

    print(
      '🔍 [PersonalDataService.uploadProfilePhoto] Status Code: ${response.statusCode}',
    );
    print(
      '🔍 [PersonalDataService.uploadProfilePhoto] Response Body: ${responseBody.body}',
    );

    if (response.statusCode == 200) {
      final json = jsonDecode(responseBody.body);
      print(
        '🔍 [PersonalDataService.uploadProfilePhoto] Datos parseados: $json',
      );
      return json['profile_photo_path'] ?? '';
    } else {
      print(
        '❌ [PersonalDataService.uploadProfilePhoto] Error ${response.statusCode}: ${responseBody.body}',
      );
      throw Exception('Error al subir foto: ${responseBody.body}');
    }
  }
}
