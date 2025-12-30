import 'package:flutter/material.dart';
import 'package:mongo_dart/mongo_dart.dart' as mongo;

const String mongoUri = 'mongodb+srv://valenciaalmeidavictor:Valencia123%4012@consultoria.2j6jkko.mongodb.net/dbrutvans?retryWrites=true&w=majority';
const String collectionName = 'users';

class ForgotPasswordPage extends StatefulWidget {
  const ForgotPasswordPage({super.key});

  @override
  State<ForgotPasswordPage> createState() => _ForgotPasswordPageState();
}

class _ForgotPasswordPageState extends State<ForgotPasswordPage> {
  final TextEditingController _emailController = TextEditingController();
  String? recoveredPassword;

  Future<void> _recoverPassword() async {
    final email = _emailController.text.trim();
    if (email.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Ingresa tu correo electrónico")),
      );
      return;
    }

    try {
      final db = await mongo.Db.create(mongoUri);
      await db.open();
      final collection = db.collection(collectionName);

      final user = await collection.findOne(mongo.where.eq('email', email));
      await db.close();

      if (user != null) {
        setState(() {
          recoveredPassword = user['password'];
        });
      } else {
        setState(() {
          recoveredPassword = null;
        });
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Correo no encontrado")),
        );
      }
    } catch (e) {
      setState(() {
        recoveredPassword = null;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Error al recuperar contraseña: $e")),
      );
    }
  }

  void _resetState() {
    setState(() {
      recoveredPassword = null;
      _emailController.clear();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Recuperar contraseña")),
      body: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          children: [
            const SizedBox(height: 40),
            const Text(
              "Ingresa tu correo electrónico registrado",
              style: TextStyle(fontSize: 18),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 30),
            TextField(
              controller: _emailController,
              decoration: const InputDecoration(
                labelText: "Correo electrónico",
                border: OutlineInputBorder(),
                prefixIcon: Icon(Icons.email),
              ),
              keyboardType: TextInputType.emailAddress,
            ),
            const SizedBox(height: 30),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: _recoverPassword,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.black87,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(30.0),
                  ),
                ),
                child: const Text("Recuperar contraseña", style: TextStyle(color: Colors.white)),
              ),
            ),
            const SizedBox(height: 30),

            // Mostrar contraseña si existe
            if (recoveredPassword != null) ...[
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.grey.shade200,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black12,
                      blurRadius: 10,
                      spreadRadius: 2,
                    )
                  ],
                ),
                child: Column(
                  children: [
                    const Text(
                      "Tu contraseña es:",
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 10),
                    Text(
                      recoveredPassword!,
                      style: const TextStyle(fontSize: 20, color: Colors.black87),
                    ),
                    const SizedBox(height: 20),
                    Center(
                      child: ElevatedButton(
                        onPressed: _resetState,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFFFF9800), // FONDO NARANJA
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(20),
                          ),
                          padding: const EdgeInsets.symmetric(horizontal: 30, vertical: 12),
                        ),
                        child: const Text(
                          "Aceptar",
                          style: TextStyle(color: Colors.white), // TEXTO BLANCO
                        ),
                      ),
                    )
                  ],
                ),
              )
            ],
          ],
        ),
      ),
    );
  }
}
