<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }




    public function logout(Request $request)
    {
        $user = Auth::user();
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($user) {
            \Log::info('Logout interceptado', ['user_id' => $user->id]);

            // Verificar configuración en tabla variables
            $tipoCorte = DB::table('variables')->value('tipo_corte'); // obtiene el primero encontrado

            \Log::info('Tipo de corte obtenido', ['tipo_corte' => $tipoCorte]);

            if ($tipoCorte == 3) {
                $this->cerrarCorteCaja($user->id);
            } else {
                \Log::info('No se cierra la caja porque tipo_corte != 3');
            }
        }

        return redirect('/login');
    }


    protected function cerrarCorteCaja($userId)
    {
        \Log::info('Cerrando corte de caja', ['user_id' => $userId]);

        $updated = DB::table('corte_caja')
            ->where('user_id', $userId)
            ->where('estado', 'abierto')
            ->update(['estado' => 'cerrado']);

        \Log::info('Corte de caja actualizado', [
            'user_id' => $userId,
            'registros_afectados' => $updated
        ]);
    }

        protected function authenticated(Request $request, $user)
    {
        // Revisamos si ya hay una caja abierta para este usuario hoy
        $caja = DB::table('corte_caja')
            ->where('user_id', $user->id)
            ->whereDate('created_at', now()->toDateString())
            ->where('estado', 'abierto')
            ->first();

        if (!$caja) {
            // No hay caja abierta → redirigir a formulario para saldo inicial
            return redirect()->route('caja.abierta'); // Ruta que muestra el formulario
        }

        // Si ya hay caja abierta, continuar al home normalmente
        return redirect()->intended($this->redirectPath());
    }

}
