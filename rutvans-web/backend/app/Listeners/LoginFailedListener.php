<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;

class LoginFailedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        // Agregar mensaje personalizado a la sesión
        if (request()->expectsJson()) {
            return;
        }

        $email = request()->input('email');
        
        if ($email && \App\Models\User::where('email', $email)->exists()) {
            // Usuario existe pero contraseña incorrecta
            session()->flash('error', '🔒 La contraseña es incorrecta. <a href="' . route('password.request') . '" class="text-primary">¿Olvidaste tu contraseña?</a>');
        } else {
            // Usuario no existe
            session()->flash('error', '❌ No encontramos una cuenta con ese email. <a href="' . route('register') . '" class="text-primary">¿Quieres crear una cuenta?</a>');
        }
    }
}
