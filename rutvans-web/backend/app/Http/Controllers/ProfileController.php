<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.index', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        try {
            $user = auth()->user();

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'phone_number' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'photo' => 'nullable|image|max:2048',
            ]);

            $data = $request->only('name', 'email', 'phone_number', 'address');

            if ($request->hasFile('photo')) {
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $path = $request->file('photo')->store('profile-photos', 'public');
                $data['profile_photo_path'] = $path;
            }

            $user->update($data);

            return response()->json(['message' => 'Perfil actualizado correctamente']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'email')) {
                return response()->json(['message' => 'El correo electrónico ya está en uso.'], 409);
            }
            return response()->json(['message' => 'Error al actualizar el perfil.'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ocurrió un error inesperado.'], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|confirmed|min:8',
            ]);

            if (!Hash::check($request->current_password, Auth::user()->password)) {
                return response()->json([
                    'errors' => ['current_password' => ['La contraseña actual es incorrecta']]
                ], 422);
            }

            Auth::user()->update(['password' => Hash::make($request->password)]);

            return response()->json(['message' => 'Contraseña actualizada exitosamente']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ocurrió un error al actualizar la contraseña.'], 500);
        }
    }

    public function showSessions()
    {
        \Carbon\Carbon::setLocale('es');

        $userId = auth()->id();

        $sessions = DB::table('sessions')
            ->where('user_id', $userId)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                $parsed = $this->parseUserAgent($session->user_agent);

                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'last_activity' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'agent' => $parsed['agent'],
                    'device' => $parsed['device'],
                    'is_current_device' => $session->id === session()->getId(),
                ];
            });

        return response()->json($sessions);
    }

    private function parseUserAgent($ua)
    {
        $os = 'Desconocido';
        $browser = 'Desconocido';
        $device = 'desktop';

        // Sistema Operativo
        if (preg_match('/Windows NT 10.0/i', $ua)) $os = 'Windows 10';
        elseif (preg_match('/Windows NT 6.1/i', $ua)) $os = 'Windows 7';
        elseif (preg_match('/Mac OS X/i', $ua)) $os = 'Mac OS';
        elseif (preg_match('/Android/i', $ua)) {
            $os = 'Android';
            $device = 'mobile';
        }
        elseif (preg_match('/iPhone/i', $ua)) {
            $os = 'iOS';
            $device = 'mobile';
        }

        // Navegador
        if (preg_match('/OPR\//i', $ua)) $browser = 'Opera';
        elseif (preg_match('/Edg\//i', $ua)) $browser = 'Edge';
        elseif (preg_match('/Chrome/i', $ua)) $browser = 'Chrome';
        elseif (preg_match('/Safari/i', $ua)) $browser = 'Safari';
        elseif (preg_match('/Firefox/i', $ua)) $browser = 'Firefox';
        elseif (preg_match('/MSIE/i', $ua)) $browser = 'Internet Explorer';

        return [
            'agent' => "{$browser} en {$os}",
            'device' => $device
        ];
    }

    public function destroySession($id)
    {
        $session = DB::table('sessions')->where('id', $id)->first();

        if ($session && $session->user_id === auth()->id() && $session->id !== session()->getId()) {
            DB::table('sessions')->where('id', $id)->delete();
            return response()->json(['message' => 'Sesión cerrada con éxito']);
        }

        return response()->json(['message' => 'No autorizado o sesión inválida'], 403);
    }

    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['valid' => false, 'message' => 'Contraseña incorrecta'], 422);
        }

        return response()->json(['valid' => true]);
    }

    public function logoutOtherSessions(Request $request)
    {
        try {
            $request->validate(['password' => 'required']);

            if (!Hash::check($request->password, Auth::user()->password)) {
                return response()->json(['message' => 'La contraseña es incorrecta.'], 422);
            }

            DB::table('sessions')
                ->where('user_id', Auth::id())
                ->where('id', '!=', session()->getId())
                ->delete();

            return response()->json(['message' => 'Se cerraron correctamente otras sesiones.']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al cerrar otras sesiones.'], 500);
        }
    }




public function eliminarUsuario(Request $request)
{
    $request->validate([
        'password' => 'required',
    ]);

    $user = Auth::user();

    if (!\Hash::check($request->password, $user->password)) {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'La contraseña es incorrecta'], 422);
        }
        return back()->withErrors(['password' => 'La contraseña es incorrecta']);
    }

    Auth::guard('web')->logout();
    $user->delete();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    if ($request->expectsJson()) {
        return response()->json(['message' => 'Cuenta eliminada correctamente.']);
    }

    return redirect()->route('login')->with('status', 'Tu cuenta ha sido eliminada correctamente.');
}

}
