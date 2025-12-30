<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;


class AuthAPIController extends Controller
{
public function login_admin(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    /** @var \App\Models\User $user */
    $user = Auth::user();

    $token = $user->createToken('flutter-token')->plainTextToken;

 return response()->json([
    'token' => $token,
    'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'roles' => $user->roles()->get(['id', 'name']),
        'companies' => $user->companies()
            ->select('companies.id', 'companies.name', 'company_users.role', 'company_users.status')
            ->get(),
    ]
]);

}


public function user(Request $request)
{
    $user = $request->user();
    $user->load(['roles', 'companies']);

    $profilePhotoUrl = $user->profile_photo_path
        ? url('storage/' . $user->profile_photo_path)
        : null;

    $userArray = $user->toArray();
    $userArray['profile_photo_url'] = $profilePhotoUrl;

    $userArray['roles'] = $user->roles->map(function ($role) {
        return [
            'id' => $role->id,
            'name' => $role->name,
        ];
    })->toArray();

    $userArray['companies'] = $user->companies->filter(function($company) {
        return $company->pivot->status === 'active';
    })->map(function ($company) {
        return [
            'id' => $company->id,
            'name' => $company->name,
            'role' => $company->pivot->role,
            'status' => $company->pivot->status,
        ];
    })->values()->toArray();

    return response()->json($userArray);
}
  public function logout(Request $request)
{
    // Elimina solo el token actual (el que se usó en la petición)
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Sesión cerrada correctamente']);
}

public function validateToken(Request $request)
{
    $authHeader = $request->header('Authorization');

    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
        return response()->json(['valid' => false, 'message' => 'Token no enviado'], 401);
    }

    $plainToken = str_replace('Bearer ', '', $authHeader);

    $token = PersonalAccessToken::findToken($plainToken);

    if (!$token) {
        return response()->json(['valid' => false, 'message' => 'Token inválido o expirado'], 401);
    }

    return response()->json(['valid' => true, 'message' => 'Token válido'], 200);
}

}
