<?php

namespace App\Http\Middleware;

// use App\Http\Controllers\DashboardController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Redirigir según el rol del usuario usando Spatie
                if ($user->hasRole('super-admin')) {
                    return redirect()->route('profile.edit');
                } elseif ($user->hasRole('admin')) {
                    return redirect()->route('dashboard');
                } elseif ($user->hasRole('coordinate')) {
                    return redirect()->route('coordinator.dashboard');
                } elseif ($user->hasRole('driver')) {
                    return redirect()->route('dashboard.role');
                } elseif ($user->hasRole('cashier')) {
                    return redirect()->route('dashboard.role');
                } elseif ($user->hasRole('client')) {
                    return redirect()->route('dashboard.role');
                } else {
                    // Usuario sin rol asignado
                    return redirect()->route('profile.edit');
                }
            }
        }

        return $next($request);
    }
}
