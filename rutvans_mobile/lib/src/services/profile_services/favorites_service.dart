import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

class FavoritesService {
  static const String _favoritesKey = 'favorite_travel_ids'; // Clave para guardar IDs en SharedPrefs

  // Obtiene la lista de IDs de favoritos desde SharedPreferences
  static Future<Set<String>> _getFavoriteIds() async {
    final prefs = await SharedPreferences.getInstance();
    final idsJson = prefs.getString(_favoritesKey) ?? '[]';
    final List<dynamic> idsList = jsonDecode(idsJson);
    return idsList.cast<String>().toSet();
  }

  // Guarda la lista de IDs en SharedPreferences
  static Future<void> _saveFavoriteIds(Set<String> ids) async {
    final prefs = await SharedPreferences.getInstance();
    final idsJson = jsonEncode(ids.toList());
    await prefs.setString(_favoritesKey, idsJson);
  }

  // Verifica si un travelId es favorito
  static Future<bool> isFavorite(String travelId) async {
    final favoriteIds = await _getFavoriteIds();
    return favoriteIds.contains(travelId);
  }

  // Toggle: agrega o quita un travelId de favoritos
  static Future<bool> toggleFavorite(String travelId) async {
    final favoriteIds = await _getFavoriteIds();
    final wasFavorite = favoriteIds.contains(travelId);
    if (wasFavorite) {
      favoriteIds.remove(travelId);
    } else {
      favoriteIds.add(travelId);
    }
    await _saveFavoriteIds(favoriteIds);
    return !wasFavorite; // Retorna si ahora es favorito
  }

  // Obtiene todos los IDs de favoritos
  static Future<Set<String>> getAllFavoriteIds() async {
    return await _getFavoriteIds();
  }
}