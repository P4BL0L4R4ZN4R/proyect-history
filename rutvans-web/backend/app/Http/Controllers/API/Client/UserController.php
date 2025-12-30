<?php

namespace App\Http\Controllers\API\Client;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone_number' => $request->phone_number,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'token' => $token,
        ]);
    }

    public function getUser(Request $request)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['error' => 'Falta el user_id'], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Generar URL completa para la foto de perfil si existe
        $profilePhotoUrl = null;
        if ($user->profile_photo_path) {
            $baseUrl = request()->getSchemeAndHttpHost(); // Obtiene https://determined-impactive-naomi.ngrok-free.dev
            $profilePhotoUrl = $baseUrl . '/storage/' . $user->profile_photo_path;
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'address' => $user->address,
            'profile_photo_path' => $profilePhotoUrl,
        ], 200);
    }

    public function updateUser(Request $request)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['error' => 'Falta el user_id'], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone_number' => 'sometimes|nullable|string|max:255',
            'address' => 'sometimes|nullable|string|max:255',
        ]);

        $user->update($validated);

        return response()->json(['message' => 'Datos actualizados con exito'], 200);
    }

    public function uploadPhoto(Request $request)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['error' => 'Falta el user_id'], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = 'profile_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs("users/{$user->id}", $fileName, 'public');

            // === SINCRONIZACIÓN AUTOMÁTICA PARA DESARROLLO ===
            // Copiar automáticamente a public/storage para que funcione sin enlaces simbólicos
            $this->ensureStorageSync($path);

            // Guardar solo la ruta relativa en la base de datos
            $user->profile_photo_path = $path;
            $user->save();

            // Generar URL completa con ngrok
            $baseUrl = request()->getSchemeAndHttpHost(); // Obtiene https://determined-impactive-naomi.ngrok-free.dev
            $photoUrl = $baseUrl . '/storage/' . $path;

            return response()->json([
                'profile_photo_path' => $photoUrl
            ], 200);
        }

        return response()->json(['error' => 'No se proporciono una foto'], 400);
    }

    // Verificar contraseña actual
    public function verifyPassword(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'current_password' => 'required|string'
            ]);

            $user = User::find($request->user_id);
            
            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            $isValid = Hash::check($request->current_password, $user->password);

            return response()->json([
                'status' => 'success',
                'valid' => $isValid,
                'message' => $isValid ? 'Contraseña válida' : 'Contraseña incorrecta'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    // Cambiar contraseña
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'new_password' => 'required|string|min:6'
            ]);

            $user = User::find($request->user_id);
            
            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Contraseña actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    // Actualizar contraseña con verificación
    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6'
            ]);

            $user = User::find($request->user_id);
            
            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            // Verificar contraseña actual
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'error' => 'La contraseña actual es incorrecta'
                ], 400);
            }

            // Actualizar contraseña
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Contraseña actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Sincroniza automáticamente los archivos de storage/app/public hacia public/storage
     * Esto resuelve el problema de enlaces simbólicos en entornos de desarrollo
     */
    private function ensureStorageSync($relativePath)
    {
        try {
            $sourcePath = storage_path('app/public/' . $relativePath);
            $destinationPath = public_path('storage/' . $relativePath);

            // Crear directorio de destino si no existe
            $destinationDir = dirname($destinationPath);
            if (!file_exists($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            // Copiar el archivo si la fuente existe
            if (file_exists($sourcePath)) {
                copy($sourcePath, $destinationPath);
                Log::info("Storage sync: Copied {$relativePath} to public/storage");
            }
        } catch (\Exception $e) {
            Log::warning("Storage sync failed for {$relativePath}: " . $e->getMessage());
        }
    }
}
