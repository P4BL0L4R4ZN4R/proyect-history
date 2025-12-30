<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Personalizar autenticación fallida
        Fortify::authenticateUsing(function ($request) {
            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                // Email verificado?
                if (!$user->hasVerifiedEmail()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'email' => ['📧 Debes verificar tu email antes de iniciar sesión. <a href="' . route('verification.notice') . '" class="text-primary">Reenviar email de verificación</a>'],
                    ]);
                }
                return $user;
            }

            // Determinar tipo de error específico
            if ($user) {
                // Usuario existe pero contraseña incorrecta
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'password' => ['🔒 La contraseña es incorrecta. <a href="' . route('password.request') . '" class="text-primary">¿Olvidaste tu contraseña?</a>'],
                ]);
            } else {
                // Usuario no existe
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => ['❌ No encontramos una cuenta con ese email. <a href="' . route('register') . '" class="text-primary">¿Quieres crear una cuenta?</a>'],
                ]);
            }
        });

        // Redirección personalizada después del login según el rol
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                $user = $request->user();

                if (!$user) {
                    return redirect()->route('login');
                }

                // Redirigir según el rol del usuario usando Spatie
                if ($user->hasRole('super-admin')) {
                    return redirect()->route('profile.edit')->with('success', '¡Bienvenido Super Admin! Puedes gestionar roles y permisos.');
                } elseif ($user->hasRole('admin')) {
                    return redirect()->route('dashboard')->with('success', '¡Bienvenido Admin!');
                } elseif ($user->hasRole('coordinate')) {
                    return redirect()->route('coordinator.dashboard');
                } elseif ($user->hasRole('driver')) {
                    return redirect()->route('dashboard.role')->with('success', '¡Bienvenido Conductor!');
                } elseif ($user->hasRole('cashier')) {
                    return redirect()->route('dashboard.role')->with('success', '¡Bienvenido Cajero!');
                } elseif ($user->hasRole('client')) {
                    return redirect()->route('dashboard.role')->with('success', '¡Bienvenido Cliente!');
                } else {
                    // Usuario sin rol asignado
                    return redirect()->route('profile.edit')->with('warning', 'Tu cuenta no tiene un rol asignado. Contacta con el administrador.');
                }
            }
        });

        // Redirección personalizada después del registro
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect()->route('verification.notice')->with('success', '✅ ¡Cuenta creada exitosamente! Te hemos enviado un email de verificación.');
            }
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
