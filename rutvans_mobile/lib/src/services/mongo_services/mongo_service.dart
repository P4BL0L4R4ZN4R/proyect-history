import 'package:mongo_dart/mongo_dart.dart';

class MongoService {
  static final MongoService _instance = MongoService._internal();
  factory MongoService() => _instance;
  MongoService._internal();

  late Db _db;
  bool _isConnected = false;

  Future<void> connect() async {
    if (!_isConnected) {
      // Reemplaza con tu URI de MongoDB Atlas
      const connectionString = 'mongodb+srv://valenciaalmeidavictor:Valencia123%4012@consultoria.2j6jkko.mongodb.net/dbrutvans?retryWrites=true&w=majority';
      
      _db = await Db.create(connectionString);
      await _db.open();
      _isConnected = true;
    }
  }

  Future<Map<String, dynamic>?> getRouteUnitSchedule(String id) async {
    try {
      await connect();
      var collection = _db.collection('route_unit_schedule');
      var result = await collection.findOne(where.id(ObjectId.fromHexString(id)));
      return result;
    } catch (e) {
      print('Error fetching data: $e');
      return null;
    }
  }

  Future<void> disconnect() async {
    if (_isConnected) {
      await _db.close();
      _isConnected = false;
    }
  }
}