<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRoleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Excluir rutas de almacenamiento público (imágenes, archivos) para evitar bloqueos 403
        if ($request->is('storage/*')) {
            return $next($request);
        }

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Si no se especifican roles, permitir el acceso
        if (empty($roles)) {
            return $next($request);
        }

        // Verificar si el usuario tiene alguno de los roles requeridos
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Si el usuario no tiene permisos, mostrar vista personalizada
        if ($request->expectsJson()) {
            return response()->json(['message' => 'No tienes permisos para acceder a esta sección.'], 403);
        }

        // Mostrar vista personalizada de error 403
        return response()->view('errors.unauthorized', [
            'message' => 'No tienes los permisos necesarios para acceder a esta sección.'
        ], 403);
    }
}
