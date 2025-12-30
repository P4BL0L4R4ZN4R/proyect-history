<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class ControlCortes
{
    /**
     * Handle an incoming request.
     */

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) return $next($request);

        $excluded = [
            'caja.abierta', // nombre
            'caja.store',   // nombre
            'logout', 'login',
            'register', 'password.*'
        ];

        $currentName = optional($request->route())->getName();
        $currentUri = $request->path();

        // Evitar middleware en rutas excluidas
        if (in_array($currentName, $excluded) || in_array($currentUri, ['turno','turnoinicio'])) {
            return $next($request);
        }

        // Verificar caja abierta
        $cajaAbierta = DB::table('corte_caja')
            ->where('user_id', $user->id)
            ->where('estado', 'abierto')
            ->latest('created_at')
            ->first();

        if (!$cajaAbierta) {
            Session::flash('error', 'Debe abrir una caja antes de continuar.');
            return redirect()->route('caja.abierta'); // ⚠️ Nunca a store
        }

        Session::put('caja_actual', [
            'id' => $cajaAbierta->id,
            'saldo_inicio' => $cajaAbierta->saldo_inicio,
            'fecha_apertura' => $cajaAbierta->created_at
        ]);

        return $next($request);
    }

    /**
     * Verifica si la ruta está excluida de la verificación de caja
     */
    protected function isRouteExcluded($currentRoute, $excludedRoutes): bool
    {
        if (!$currentRoute) {
            return false;
        }

        foreach ($excludedRoutes as $pattern) {
            if (str_contains($pattern, '*')) {
                // Patrón con wildcard (ej: 'caja.*')
                $pattern = str_replace('*', '.*', $pattern);
                if (preg_match('#^' . $pattern . '$#', $currentRoute)) {
                    return true;
                }
            } elseif ($currentRoute === $pattern) {
                // Coincidencia exacta
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica si existe un corte más reciente cerrado
     */
    protected function verificarCorteReciente($userId): array
    {
        // Obtener la última caja del usuario (abierta o cerrada)
        $ultimaCajaUsuario = DB::table('corte_caja')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Si el usuario no tiene ninguna caja, no hay problema
        if (!$ultimaCajaUsuario) {
            return [
                'existe' => false,
                'mensaje' => ''
            ];
        }

        // Obtener el último corte global de cualquier usuario
        $ultimoCorteGlobal = DB::table('corte_caja')
            ->orderBy('created_at', 'desc')
            ->first();

        // Si no hay corte global o es el mismo que el del usuario
        if (!$ultimoCorteGlobal || $ultimoCorteGlobal->id === $ultimaCajaUsuario->id) {
            return [
                'existe' => false,
                'mensaje' => ''
            ];
        }

        // Verificar si el último corte global es más reciente y está cerrado
        $fechaCorteGlobal = Carbon::parse($ultimoCorteGlobal->created_at);
        $fechaCajaUsuario = Carbon::parse($ultimaCajaUsuario->created_at);

        if ($ultimoCorteGlobal->estado === 'cerrado' &&
            $fechaCorteGlobal->greaterThan($fechaCajaUsuario)) {

            $diferenciaTiempo = $this->calcularDiferenciaTiempo($fechaCajaUsuario, $fechaCorteGlobal);

            return [
                'existe' => true,
                'mensaje' => "Existe un corte de caja más reciente realizado {$diferenciaTiempo}. Debe abrir una nueva caja para continuar operando.",
                'corte_reciente' => $ultimoCorteGlobal,
                'caja_actual' => $ultimaCajaUsuario
            ];
        }

        return [
            'existe' => false,
            'mensaje' => ''
        ];
    }

    /**
     * Verifica si una caja abierta está obsoleta
     */
    protected function cajaObsoleta($cajaAbierta): bool
    {
        // Si la caja ya está cerrada, no puede estar obsoleta
        if ($cajaAbierta->estado !== 'abierto') {
            return false;
        }

        // Obtener el último corte global cerrado
        $ultimoCorteCerrado = DB::table('corte_caja')
            ->where('estado', 'cerrado')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$ultimoCorteCerrado) {
            return false;
        }

        $fechaCorte = Carbon::parse($ultimoCorteCerrado->created_at);
        $fechaCaja = Carbon::parse($cajaAbierta->created_at);

        // La caja es obsoleta si hay un corte cerrado más reciente
        return $fechaCorte->greaterThan($fechaCaja);
    }

    /**
     * Calcula la diferencia de tiempo entre dos fechas en formato legible
     */
    protected function calcularDiferenciaTiempo($fechaAnterior, $fechaPosterior): string
    {
        $diff = $fechaAnterior->diff($fechaPosterior);

        if ($diff->days > 0) {
            return "hace {$diff->days} día" . ($diff->days > 1 ? 's' : '');
        } elseif ($diff->h > 0) {
            return "hace {$diff->h} hora" . ($diff->h > 1 ? 's' : '');
        } else {
            return "hace {$diff->i} minuto" . ($diff->i > 1 ? 's' : '');
        }
    }

    /**
     * Obtener información de la caja actual del usuario
     */
    public static function obtenerCajaActual($userId)
    {
        $cajaAbierta = DB::table('corte_caja')
            ->where('user_id', $userId)
            ->where('estado', 'abierto')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$cajaAbierta) {
            return null;
        }

        // Verificar que la caja no esté obsoleta
        $middleware = new self();
        if ($middleware->cajaObsoleta($cajaAbierta)) {
            return null;
        }

        return $cajaAbierta;
    }

    /**
     * Verificar si el usuario puede operar (método estático para usar en otros lugares)
     */
    public static function puedeOperar($userId): bool
    {
        $caja = self::obtenerCajaActual($userId);
        return $caja !== null;
    }

    /**
     * Obtener el último corte cerrado del sistema
     */
    public static function obtenerUltimoCorte()
    {
        return DB::table('corte_caja')
            ->where('estado', 'cerrado')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Forzar cierre de caja obsoleta (para usar en el comando)
     */
    public static function forzarCierreCajaObsoleta($cajaId)
    {
        return DB::table('corte_caja')
            ->where('id', $cajaId)
            ->update([
                'estado' => 'cerrado',
                'cerrado_automaticamente' => true,
                'updated_at' => now()
            ]);
    }
}
