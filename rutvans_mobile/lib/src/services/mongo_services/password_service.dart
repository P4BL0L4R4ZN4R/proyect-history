// import 'package:bcrypt/bcrypt.dart';
import 'package:mongo_dart/mongo_dart.dart';
import 'package:rutvans_mobile/src/models/user_session.dart';

class PasswordService {
  static const String _connectionString = 'mongodb+srv://valenciaalmeidavictor:Valencia123%4012@consultoria.2j6jkko.mongodb.net/dbrutvans?retryWrites=true&w=majority';
  Db? _db;

  Future<bool> verifyCurrentPassword(String inputPassword) async {
    final userId = UserSession().getUserId();
    if (userId == null) {
      throw Exception('No hay usuario logeado');
    }

    try {
      await _connect();
      final collection = _db!.collection('users');
      final user = await collection.findOne(
        where.id(ObjectId.parse(userId)),
      );

      if (user == null || user['password'] == null) {
        throw Exception('Usuario o contraseña no encontrado');
      }

      final storedHash = user['password'].toString();
      // final isValid = BCrypt.checkpw(inputPassword, storedHash);
      final isValid = inputPassword == storedHash;

      return isValid;
    } catch (e) {
      throw Exception('Error al verificar contraseña: $e');
    } finally {
      await _close();
    }
  }

  Future<void> updatePassword(String newPassword) async {
    final userId = UserSession().getUserId();
    if (userId == null) {
      throw Exception('No hay usuario logeado');
    }

    try {
      await _connect();
      final collection = _db!.collection('users');
      // final hashedPassword = BCrypt.hashpw(newPassword, BCrypt.gensalt());

      final result = await collection.updateOne(
        where.id(ObjectId.parse(userId)),
        modify.set('password', newPassword), // hashedPassword
      );

      if (result.nModified == 0) {
        throw Exception('No se pudo actualizar la contraseña');
      }
    } catch (e) {
      throw Exception('Error al actualizar contraseña: $e');
    } finally {
      await _close();
    }
  }

  Future<void> _connect() async {
    if (_db == null || !_db!.isConnected) {
      _db = await Db.create(_connectionString);
      await _db!.open();
    }
  }

  Future<void> _close() async {
    if (_db != null && _db!.isConnected) {
      await _db!.close();
      _db = null;
    }
  }
}