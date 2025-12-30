<?php
namespace App\Http\Controllers\API\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PerfilApiController extends Controller
{
    // Método para obtener el perfil del usuario autenticado
   public function perfil(Request $request)
{
    $user = $request->user();

    $profilePhotoUrl = null;
    if ($user->profile_photo_path) {
        $profilePhotoUrl = url('storage/' . $user->profile_photo_path);
    }

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'address' => $user->address,
        'phone_number' => $user->phone_number,
        'profile_photo_url' => $profilePhotoUrl,
        'profile_photo_path' => $user->profile_photo_path,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
    ]);
}


    // Método para actualizar el perfil del usuario autenticado
    public function actualizarPerfil(Request $request)
    {
        $user = $request->user();

        // Validación de campos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'address' => 'nullable|string|max:500',
            'phone_number' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|max:2048', // max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Actualizar campos básicos
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->address = $request->input('address');
        $user->phone_number = $request->input('phone_number');

        // Si envían foto de perfil, guardarla
        if ($request->hasFile('profile_photo')) {
            // Eliminar foto vieja si existe
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Guardar nueva foto
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user' => $user
        ]);
    }
}
