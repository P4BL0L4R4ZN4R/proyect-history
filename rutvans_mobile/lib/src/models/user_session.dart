class UserSession {
  static final UserSession _instance = UserSession._internal();
  String? _userId;

  factory UserSession() {
    return _instance;
  }

  UserSession._internal();

  void setUserId(String userId) {
    _userId = userId;
  }

  String? getUserId() {
    return _userId;
  }

  void clearSession() {
    _userId = null;
  }
}