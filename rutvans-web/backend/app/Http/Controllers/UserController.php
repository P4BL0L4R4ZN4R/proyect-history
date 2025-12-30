<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{
    /**
     * Muestra la lista de usuarios.
     */
    public function index()
    {
        $usuarios = User::all();
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        return view('usuarios.create');
    }

    /**
     * Guarda un nuevo usuario en la base de datos y envía correo de verificación.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $usuario = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'address' => $request->input('address'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Disparar evento para enviar correo de verificación
        event(new Registered($usuario));

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente y correo de verificación enviado.');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     */
    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Actualiza un usuario en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $usuario = User::findOrFail($id);

        $usuario->name = $request->input('name');
        $usuario->email = $request->input('email');
        $usuario->phone_number = $request->input('phone_number');
        $usuario->address = $request->input('address');

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->input('password'));
        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Elimina un usuario de la base de datos.
     */
    public function destroy(User $usuario)
    {
        try {
            $usuario->delete();
            return response()->json(['success' => true, 'message' => '¡Usuario eliminado correctamente!']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar usuario: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar el usuario'], 500);
        }
    }

    /**
     * Devuelve todos los usuarios en formato JSON (API).
     */
    public function apiIndex()
    {
        return response()->json(User::all());
    }
}
