import 'package:mongo_dart/mongo_dart.dart';

class UserService {
  static final UserService _instance = UserService._internal();
  factory UserService() => _instance;
  UserService._internal();

  static const _connectionString =
      'mongodb+srv://valenciaalmeidavictor:Valencia123%4012@consultoria.2j6jkko.mongodb.net/dbrutvans?retryWrites=true&w=majority';
  late Db _db;
  late DbCollection _usersCollection;
  bool _isConnected = false;

  Future<void> connect() async {
    if (!_isConnected) {
      try {
        _db = await Db.create(_connectionString);
        await _db.open();
        _usersCollection = _db.collection('personal_data');
        _isConnected = true;
        print('Conexión a MongoDB establecida para UserService');
      } catch (e) {
        print('Error al conectar a MongoDB en UserService: $e');
        rethrow;
      }
    }
  }

  Future<Map<String, dynamic>?> getUserData(String email) async {
    try {
      await connect();
      final result = await _usersCollection.findOne(where.eq('email', email));
      return result;
    } catch (e) {
      print('Error al obtener datos de usuario: $e');
      return null;
    }
  }

  Future<bool> saveUserData(Map<String, dynamic> userData) async {
    try {
      await connect();
      if (!_isConnected) {
        await _db.open();
      }
      final existingUser = await _usersCollection.findOne(where.eq('email', userData['email']));
      if (existingUser != null) {
        // Actualizar usuario existente
        await _usersCollection.update(
          where.eq('email', userData['email']),
          userData,
          upsert: true,
        );
      } else {
        // Insertar nuevo usuario
        await _usersCollection.insert(userData);
      }
      return true;
    } catch (e) {
      print('Error al guardar datos de usuario: $e');
      return false;
    }
  }

  Future<void> disconnect() async {
    if (_isConnected) {
      try {
        await _db.close();
        _isConnected = false;
        print('Conexión cerrada en UserService');
      } catch (e) {
        print('Error al cerrar conexión en UserService: $e');
      }
    }
  }
}