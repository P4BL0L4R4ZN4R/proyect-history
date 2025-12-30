<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Imagick;

class ConfiguracionController extends Controller
{
    protected $imageSettings = [
        'directory' => 'images/configuracion',
        'default' => 'images/logo-default.jpg',
        'max_size' => 2048,
    ];

    public function edit()
    {
        // Obtener configuración de variables con join a tipo_corte
        $config = DB::table('variables')
            ->leftJoin('tipo_corte', 'variables.tipo_corte', '=', 'tipo_corte.id')
            //por aqui las 2 columnas: alertas e impresion_ticket deberia de estar mandando a front
            ->select('variables.*', 'tipo_corte.tipo as tipo_corte_nombre')
            ->first();

        // Obtener todos los tipos de corte
        $tiposCorte = DB::table('tipo_corte')->get();

        log::info('Mostrando configuración actual:', $config ? (array)$config : ['no existe']);

        return view('configuracion.edit', [
            'config' => $config,
            'tiposCorte' => $tiposCorte,
            'currentLogo' => $this->getCurrentLogoPath($config),
            'imageSettings' => $this->imageSettings
        ]);
    }

    public function update(Request $request)
    {
        Log::info('=== INICIANDO ACTUALIZACIÓN DE CONFIGURACIÓN ===');
        Log::info('Datos recibidos en request:', $request->all());
        Log::info('Archivos recibidos:', $request->file() ? array_keys($request->file()) : ['ninguno']);

        // Validación básica de campos comunes
        $validator = Validator::make($request->all(), [
            'empresa' => 'required|string|max:50',
            'sucursal' => 'required|string|max:50',
            'tipo_corte' => 'required|exists:tipo_corte,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:'.$this->imageSettings['max_size'],
            'horarios' => 'nullable|string|max:100',
            'hora' => 'nullable|integer|min:1|max:12',
            'dias' => 'nullable|array',
            'dias.*' => 'sometimes|string|in:lun,mar,mie,jue,vie,sab,dom',
            'dias_excluir' => 'nullable|array',
            'dias_excluir.*' => 'sometimes|date_format:Y-m-d',
            'multiple_horarios' => 'nullable|string|max:255',
            'excluir_festivos' => 'nullable|boolean',
            'preset_dias' => 'nullable|string' // Agregado para manejar presets
        ]);

        if ($validator->fails()) {
            Log::warning('Error de validación:', $validator->errors()->toArray());
            return redirect()->route('configuracion.edit')
                ->withErrors($validator)
                ->with('error', 'Error de validación: ' . $validator->errors()->first());
        }

        try {
            DB::beginTransaction();

            // 1. ACTUALIZAR TIPO DE CORTE
            Log::info('Actualizando tipo de corte ID: ' . $request->tipo_corte);
            $this->actualizarTipoCorte($request);

            // 2. Actualizar configuración en tabla variables
            $config = DB::table('variables')->first();
            Log::info('Configuración existente:', $config ? (array)$config : ['no existe']);

            $updateData = [
                'empresa' => $request->empresa,
                'sucursal' => $request->sucursal,
                'tipo_corte' => $request->tipo_corte,

                'alertas' => $request->input('alertas', 'off'), // asegura valor
                'impresion_ticket' => $request->input('impresion_ticket', 'off'), // asegura valor
                'updated_at' => now()
            ];

            // Manejo del logo
            if ($request->hasFile('logo')) {
                Log::info('Procesando upload de logo');
                $updateData['imagen'] = $this->updateLogo($request->file('logo'), $config->imagen ?? null);
                Log::info('Logo actualizado: ' . $updateData['imagen']);
            }

            if ($config) {
                DB::table('variables')
                    ->where('id', $config->id)
                    ->update($updateData);
                Log::info('Configuración actualizada en tabla variables');
            } else {
                $updateData['created_at'] = now();
                DB::table('variables')->insert($updateData);
                Log::info('Nueva configuración creada en tabla variables');
            }

            DB::commit();
            $this->clearConfigCache();

            Log::info('=== ACTUALIZACIÓN COMPLETADA EXITOSAMENTE ===');
            return redirect()->route('configuracion.edit')
                ->with('success', 'Configuración actualizada correctamente');


        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar configuración: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            return redirect()->route('configuracion.edit')
                ->with('error', 'Error al actualizar la configuración: ' . $e->getMessage());
        }
    }

    private function actualizarTipoCorte(Request $request)
    {
        Log::info('=== ACTUALIZANDO TIPO DE CORTE ===');
        Log::info('Tipo corte ID: ' . $request->tipo_corte);
        Log::info('Datos recibidos para tipo corte:', [
            'horarios' => $request->horarios,
            'hora' => $request->hora,
            'dias' => $request->dias,
            'preset_dias' => $request->preset_dias,
            'dias_excluir' => $request->dias_excluir,
            'multiple_horarios' => $request->multiple_horarios,
            'excluir_festivos' => $request->excluir_festivos
        ]);

        // Obtener el tipo de corte actual para determinar la lógica
        $tipoCorteActual = DB::table('tipo_corte')
            ->where('id', $request->tipo_corte)
            ->first();

        if (!$tipoCorteActual) {
            throw new \Exception('Tipo de corte no encontrado con ID: ' . $request->tipo_corte);
        }

        $tipo = strtolower($tipoCorteActual->tipo);
        Log::info('Tipo de corte detectado: ' . $tipo);

        $tipoCorteData = ['updated_at' => now()];

        // 1. CORTE PARCIAL - No requiere horarios, hora ni días
        if (strpos($tipo, 'parcial') !== false) {
            Log::info('Procesando CORTE PARCIAL - Limpiando campos no requeridos');

            // Para corte parcial, limpiamos estos campos
            $tipoCorteData['horarios'] = null;
            $tipoCorteData['hora'] = null;
            $tipoCorteData['dias'] = null;
            $tipoCorteData['multiple_horarios'] = null;

            Log::info('Campos limpiados para corte parcial');
        }
        // 2. CORTE POR HORARIO - Requiere horarios y días, ignora hora
        else if (strpos($tipo, 'horario') !== false) {
            Log::info('Procesando CORTE POR HORARIO');

            if ($request->filled('horarios')) {
                $tipoCorteData['horarios'] = $request->horarios;
                Log::info('Horarios establecidos: ' . $request->horarios);
            }

            // Manejar días (desde preset o selección manual)
            $dias = $this->obtenerDiasDesdeRequest($request);
            if (!empty($dias)) {
                $tipoCorteData['dias'] = $dias;
                Log::info('Días establecidos: ' . $dias);
            }

            // Campos adicionales
            if ($request->filled('multiple_horarios')) {
                $tipoCorteData['multiple_horarios'] = $request->multiple_horarios;
                Log::info('Múltiples horarios establecidos: ' . $request->multiple_horarios);
            }

            // Limpiar campo hora para este tipo
            $tipoCorteData['hora'] = null;
            Log::info('Campo hora limpiado para corte por horario');
        }
        // 3. CORTE POR HORA INTERVALO - Requiere hora y días, ignora horarios
        else if (strpos($tipo, 'hora') !== false) {
            Log::info('Procesando CORTE POR HORA INTERVALO');

            if ($request->filled('hora')) {
                $tipoCorteData['hora'] = $request->hora . ':00'; // Formato time
                Log::info('Hora establecida: ' . $tipoCorteData['hora']);
            }

            // Manejar días (desde preset o selección manual)
            $dias = $this->obtenerDiasDesdeRequest($request);
            if (!empty($dias)) {
                $tipoCorteData['dias'] = $dias;
                Log::info('Días establecidos: ' . $dias);
            }

            // Limpiar campo horarios para este tipo
            $tipoCorteData['horarios'] = null;
            $tipoCorteData['multiple_horarios'] = null;
            Log::info('Campos horarios limpiados para corte por hora intervalo');
        } else {
            Log::warning('Tipo de corte no reconocido: ' . $tipo);
        }

        // Campos comunes para todos los tipos (si están presentes)
        if ($request->has('dias_excluir') && is_array($request->dias_excluir) && !empty($request->dias_excluir)) {
            $tipoCorteData['dias_excluir'] = implode(',', $request->dias_excluir);
            Log::info('Días a excluir establecidos: ' . $tipoCorteData['dias_excluir']);
        } else {
            $tipoCorteData['dias_excluir'] = null;
            Log::info('Días a excluir limpiados');
        }

        if ($request->has('excluir_festivos')) {
            $tipoCorteData['excluir_festivos'] = $request->excluir_festivos ? 1 : 0;
            Log::info('Excluir festivos establecido: ' . $tipoCorteData['excluir_festivos']);
        } else {
            $tipoCorteData['excluir_festivos'] = 0;
            Log::info('Excluir festivos establecido por defecto: 0');
        }

        // Actualizar tipo de corte existente
        Log::info('Actualizando tipo de corte con datos:', $tipoCorteData);

        $affected = DB::table('tipo_corte')
            ->where('id', $request->tipo_corte)
            ->update($tipoCorteData);

        if ($affected === 0) {
            Log::warning('No se actualizó ningún registro en tipo_corte');
        } else {
            Log::info('Tipo de corte actualizado exitosamente. Registros afectados: ' . $affected);
        }
    }


    private function obtenerDiasDesdeRequest(Request $request)
    {
        Log::info('Obteniendo días desde request');
        Log::info('Preset dias: ' . ($request->preset_dias ?? 'no proporcionado'));
        Log::info('Dias array: ', $request->dias ?? ['ninguno']);

        // Si hay un preset seleccionado y no es "custom"
        if ($request->filled('preset_dias') && $request->preset_dias !== 'custom') {
            Log::info('Usando preset de días: ' . $request->preset_dias);
            return $request->preset_dias;
        }

        // Si hay días seleccionados manualmente
        if ($request->has('dias') && is_array($request->dias) && !empty($request->dias)) {
            $dias = implode(',', $request->dias);
            Log::info('Usando días manuales: ' . $dias);
            return $dias;
        }

        Log::info('No se encontraron días en el request');
        return null;
    }

    protected function getCurrentLogoPath($config)
    {
        $logoPath = $config->imagen ?? $this->imageSettings['default'];

        if ($logoPath !== $this->imageSettings['default'] &&
            !File::exists(public_path($logoPath))) {
            return $this->imageSettings['default'];
        }

        return $logoPath;
    }

    protected function updateLogo($imageFile, $currentImagePath = null)
    {
        Log::info("Entrando a updateLogo con Imagick");

        $imageDir = public_path('images/configuracion');
        $faviconDir = public_path('favicons');

        // Asegurar carpetas
        if (!File::exists($imageDir)) {
            File::makeDirectory($imageDir, 0755, true);
            Log::info("Directorio de imágenes creado: $imageDir");
        }

        if (!File::exists($faviconDir)) {
            File::makeDirectory($faviconDir, 0755, true);
            Log::info("Directorio de favicons creado: $faviconDir");
        }

        // Eliminar imagen anterior
        if ($currentImagePath &&
            $currentImagePath !== 'images/logo-default.jpg' &&
            File::exists(public_path($currentImagePath))) {

            File::delete(public_path($currentImagePath));
            Log::info("Imagen anterior borrada: $currentImagePath");

            // Intentar deducir el nombre del favicon correspondiente y eliminarlo
            $oldFilename = pathinfo($currentImagePath, PATHINFO_FILENAME);
            $oldFaviconPath = $faviconDir . '/' . $oldFilename . '.ico';

            if (File::exists($oldFaviconPath)) {
                File::delete($oldFaviconPath);
                Log::info("Favicon anterior borrado: $oldFaviconPath");
            } else {
                Log::info("No se encontró favicon anterior: $oldFaviconPath");
            }
        }

        // Guardar imagen nueva
        $extension = $imageFile->getClientOriginalExtension();
        $filename = 'logo-' . time();
        $fullImageName = $filename . '.' . $extension;

        $imageFile->move($imageDir, $fullImageName);
        Log::info("Nuevo logo guardado: $fullImageName");

        $absoluteImagePath = $imageDir . '/' . $fullImageName;
        $icoPath = $faviconDir . '/' . $filename . '.ico';

        try {
            $imagick = new Imagick($absoluteImagePath);
            $imagick->resizeImage(64, 64, Imagick::FILTER_LANCZOS, 1);
            $imagick->setImageFormat('ico');
            $imagick->writeImage($icoPath);
            Log::info("Nuevo favicon generado: $icoPath");
        } catch (\Exception $e) {
            Log::error("Error al generar favicon con Imagick: " . $e->getMessage());
        }

        return 'images/configuracion/' . $fullImageName;
    }

    protected function clearConfigCache()
    {
        Cache::forget('config_global');
    }
}
