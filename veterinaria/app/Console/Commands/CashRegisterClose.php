<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CashRegisterClose extends Command
{
    protected $signature = 'cashregister:close';
    protected $description = 'Verificar y ejecutar cortes de caja automáticos';

    // 🔥 CONFIGURACIÓN PRODUCCIÓN
    protected $ventanaAntesActiva = false; // PRODUCCIÓN: sin ventana antes
    protected $minutosVentanaDespues = 5;  // 5 minutos de tolerancia después

    public function handle()
    {
        try {
            date_default_timezone_set('America/Mexico_City');
            config(['app.timezone' => 'America/Mexico_City']);

            $config = $this->obtenerConfiguracion();

            if (!$config) {
                return 0;
            }

            switch (strtolower($config->tipo)) {
                case 'horario':
                    $this->procesarCorteHorario($config);
                    break;

                case 'hora':
                    $this->procesarCorteHora($config);
                    break;

                case 'parcial':
                    break;

                default:
                    break;
            }

            return 0;

        } catch (\Exception $e) {
            return 1;
        }
    }

    protected function log($mensaje)
    {
        $this->info($mensaje);
        Log::info($mensaje);
    }

    protected function obtenerConfiguracion()
    {
        return DB::table('variables as v')
            ->join('tipo_corte as tc', 'v.tipo_corte', '=', 'tc.id')
            ->select(
                'v.id as variable_id',
                'tc.tipo',
                'tc.horarios',
                'tc.hora',
                'tc.dias',
                'tc.multiple_horarios',
                'tc.dias_excluir',
                'tc.excluir_festivos'
            )
            ->first();
    }

    protected function procesarCorteHorario($config)
    {
        $horarios = $this->obtenerHorariosConfigurados($config);

        if (empty($horarios)) {
            return;
        }

        $horaActual = Carbon::now('America/Mexico_City');

        // CONDICIÓN 1: Si HOY ya existe un corte cerrado → NO ejecutar más cortes hoy
        if ($this->hoyTieneCorteCerrado()) {
            return;
        }

        // Verificar si debemos ejecutar corte hoy
        $horarioCorte = $this->obtenerHorarioCorteActual($horarios, $horaActual);

        if (!$horarioCorte) {
            return;
        }

        // CONDICIÓN 3: Si HOY hay corte abierto → Cerrarlo
        if ($this->hoyTieneCajaAbierta()) {
            $this->ejecutarCorteConCajaAbierta($config, $horarioCorte, $horaActual);
            return;
        }

        // CONDICIÓN 2: Corte normal
        $this->ejecutarCorteNormal($config, $horarioCorte, $horaActual);
    }

    protected function hoyTieneCorteCerrado(): bool
    {
        $inicioDia = Carbon::now('America/Mexico_City')->startOfDay();
        $finDia = Carbon::now('America/Mexico_City')->endOfDay();

        return DB::table('corte_caja')
            ->where('estado', 'cerrado')
            ->whereBetween('created_at', [$inicioDia, $finDia])
            ->exists();
    }

    protected function hoyTieneCajaAbierta(): bool
    {
        $inicioDia = Carbon::now('America/Mexico_City')->startOfDay();
        $finDia = Carbon::now('America/Mexico_City')->endOfDay();

        return DB::table('corte_caja')
            ->where('estado', 'abierto')
            ->whereBetween('created_at', [$inicioDia, $finDia])
            ->exists();
    }

    protected function obtenerHorariosConfigurados($config): array
    {
        $horarios = [];

        if (!empty($config->multiple_horarios)) {
            $horarios = explode(',', $config->multiple_horarios);
        } elseif (!empty($config->horarios)) {
            if (strpos($config->horarios, ':') !== false) {
                $time = Carbon::createFromFormat('H:i:s', $config->horarios);
                $horarios[] = $time->format('H:i');
            }
        }

        $horariosLimpios = [];
        foreach ($horarios as $horario) {
            $horarioLimpio = trim($horario);
            if (preg_match('/^\d{1,2}:\d{2}$/', $horarioLimpio)) {
                $horariosLimpios[] = $horarioLimpio;
            }
        }

        sort($horariosLimpios);
        return $horariosLimpios;
    }

    protected function obtenerHorarioCorteActual($horarios, Carbon $horaActual): ?string
    {
        $ultimoHorario = end($horarios);
        reset($horarios);

        foreach ($horarios as $horario) {
            $horaCorte = Carbon::createFromFormat('H:i', $horario, 'America/Mexico_City');

            // 🔥 PRODUCCIÓN: Ventana SOLO después del horario
            $inicioVentana = $horaCorte->copy(); // Exactamente a la hora
            $finVentana = $horaCorte->copy()->addMinutes($this->minutosVentanaDespues);

            // PRIMERO: Verificar si estamos en la ventana de tiempo
            if ($horaActual->between($inicioVentana, $finVentana)) {
                return $horario;
            }

            // SEGUNDO: Verificar si ya pasó el horario pero estamos en el mismo día
            if ($horaActual->greaterThan($horaCorte) && $horaActual->isToday()) {
                // Si es el último horario del día, lo ejecutamos
                if ($horario === $ultimoHorario) {
                    return $horario;
                }
            }
        }

        return null;
    }

    protected function ejecutarCorteConCajaAbierta($config, $horarioCorte, Carbon $horaActual)
    {
        try {
            // 1. Cerrar todas las cajas abiertas de HOY
            $this->cerrarTodasLasCajasAbiertas($horaActual);

            // 2. Crear/actualizar corte
            $this->crearOActualizarCorteHoy($config, $horarioCorte, $horaActual);

        } catch (\Exception $e) {
            // Manejo silencioso de errores en producción
        }
    }

    protected function ejecutarCorteNormal($config, $horarioCorte, Carbon $horaActual)
    {
        try {
            $this->crearOActualizarCorteHoy($config, $horarioCorte, $horaActual);
        } catch (\Exception $e) {
            // Manejo silencioso de errores en producción
        }
    }

    protected function crearOActualizarCorteHoy($config, $horarioCorte, Carbon $horaActual)
    {
        try {
            // 🔥 OBTENER USUARIO DINÁMICAMENTE
            $userId = $this->obtenerUsuarioParaCorte();
            $fechaActual = $horaActual;

            // 🔥 BUSCAR CORTE ABIERTO DEL USUARIO
            $corteAbierto = DB::table('corte_caja')
                ->where('user_id', $userId)
                ->where('estado', 'abierto')
                ->whereDate('created_at', $fechaActual->format('Y-m-d'))
                ->first();

            if ($corteAbierto) {
                // 🔥 ACTUALIZAR CORTE EXISTENTE - SOLO updated_at
                $ventas = DB::table('ventas')
                    ->whereBetween('created_at', [$corteAbierto->created_at, $fechaActual])
                    ->sum('total');

                $saldoFinal = $corteAbierto->saldo_inicio + $ventas;

                DB::table('corte_caja')
                    ->where('id', $corteAbierto->id)
                    ->update([
                        'saldo_fin' => $saldoFinal,
                        'ventas_totales' => $ventas,
                        'estado' => 'cerrado',
                        'updated_at' => $fechaActual, // 🔥 SOLO updated_at se modifica
                        // created_at se mantiene igual - NO se toca
                    ]);

            } else {
                // 🔥 CREAR NUEVO CORTE - created_at y updated_at iguales
                $ultimoCorte = DB::table('corte_caja')
                    ->where('estado', 'cerrado')
                    ->orderBy('created_at', 'desc')
                    ->first();

                $saldoInicial = $ultimoCorte ? $ultimoCorte->saldo_fin : 0;

                $inicioDia = $fechaActual->copy()->startOfDay();
                $ventasHoy = DB::table('ventas')
                    ->whereBetween('created_at', [$inicioDia, $fechaActual])
                    ->sum('total');

                $saldoFinal = $saldoInicial + $ventasHoy;

                DB::table('corte_caja')->insert([
                    'user_id' => $userId,
                    'saldo_inicio' => $saldoInicial,
                    'saldo_fin' => $saldoFinal,
                    'ventas_totales' => $ventasHoy,
                    'ingresos_extra' => 0,
                    'egresos' => 0,
                    'diferencia' => 0,
                    'estado' => 'cerrado',
                    'created_at' => $fechaActual, // 🔥 Nuevo registro - created_at se establece
                    'updated_at' => $fechaActual, // 🔥 Nuevo registro - updated_at igual
                ]);
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function obtenerUsuarioParaCorte(): int
    {
        // 🔥 ESTRATEGIA 1: Buscar usuario activo reciente
        $usuarioReciente = DB::table('corte_caja')
            ->where('estado', 'abierto')
            ->orWhere('created_at', '>=', Carbon::now()->subDay())
            ->orderBy('created_at', 'desc')
            ->value('user_id');

        if ($usuarioReciente) {
            return $usuarioReciente;
        }

        // 🔥 ESTRATEGIA 2: Buscar usuario administrador
        $usuarioAdmin = DB::table('users')
            ->whereIn('id', [1, 2])
            ->whereNotNull('email_verified_at')
            ->orderBy('id')
            ->value('id');

        if ($usuarioAdmin) {
            return $usuarioAdmin;
        }

        // 🔥 ESTRATEGIA 3: Primer usuario activo del sistema
        $primerUsuario = DB::table('users')
            ->whereNotNull('email_verified_at')
            ->orderBy('id')
            ->value('id');

        if ($primerUsuario) {
            return $primerUsuario;
        }

        // 🔥 ESTRATEGIA 4: Fallback seguro
        return 1;
    }

    protected function cerrarTodasLasCajasAbiertas(Carbon $horaActual): int
    {
        $inicioDia = $horaActual->copy()->startOfDay();
        $finDia = $horaActual->copy()->endOfDay();

        $cajasAbiertas = DB::table('corte_caja')
            ->where('estado', 'abierto')
            ->whereBetween('created_at', [$inicioDia, $finDia])
            ->get();

        if ($cajasAbiertas->isEmpty()) {
            return 0;
        }

        foreach ($cajasAbiertas as $caja) {
            $this->cerrarCajaIndividual($caja, $horaActual);
        }

        return $cajasAbiertas->count();
    }

    protected function cerrarCajaIndividual($caja, Carbon $horaActual)
    {
        try {
            $ventas = DB::table('ventas')
                ->whereBetween('created_at', [$caja->created_at, $horaActual])
                ->sum('total');

            $saldoFinal = $caja->saldo_inicio + $ventas;

            DB::table('corte_caja')
                ->where('id', $caja->id)
                ->update([
                    'estado' => 'cerrado',
                    'saldo_fin' => $saldoFinal,
                    'ventas_totales' => $ventas,
                    'updated_at' => $horaActual, // 🔥 SOLO updated_at se modifica
                    // created_at se mantiene igual
                ]);

        } catch (\Exception $e) {
            // Manejo silencioso de errores en producción
        }
    }

    protected function procesarCorteHora($config)
    {
        if (empty($config->hora)) {
            return;
        }

        // Si hoy ya tiene corte cerrado → NO ejecutar
        if ($this->hoyTieneCorteCerrado()) {
            return;
        }

        // Convertir TIME a horas
        $horaTime = Carbon::createFromFormat('H:i:s', $config->hora);
        $intervaloHoras = $horaTime->hour;

        $horaActual = Carbon::now('America/Mexico_City');

        if ($this->debeEjecutarCortePorHoras($intervaloHoras)) {
            // Si hay corte abierto → Cerrarlo
            if ($this->hoyTieneCajaAbierta()) {
                $this->cerrarTodasLasCajasAbiertas($horaActual);
            }

            $this->crearOActualizarCorteHoy($config, 'Corte por horas', $horaActual);
        }
    }

    protected function debeEjecutarCortePorHoras($intervaloHoras): bool
    {
        $ultimoCorte = DB::table('corte_caja')
            ->where('estado', 'cerrado')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$ultimoCorte) {
            return true;
        }

        $horaUltimoCorte = Carbon::parse($ultimoCorte->created_at);
        $horaActual = Carbon::now('America/Mexico_City');
        $diferenciaHoras = $horaActual->diffInHours($horaUltimoCorte);

        return $diferenciaHoras >= $intervaloHoras;
    }
}
