<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StorageHelper
{
    /**
     * Obtiene la URL pública de un archivo considerando el entorno
     */
    public static function getPublicUrl($path)
    {
        if (empty($path)) {
            Log::info('[StorageHelper] getPublicUrl: path vacío');
            return null;
        }

        // En local (sin STORAGE_PATH definido), usar asset()
        if (!env('STORAGE_PATH')) {
            $url = asset('storage/' . $path);
            Log::info('[StorageHelper] getPublicUrl LOCAL: ' . $url . ' para path: ' . $path);
            return $url;
        }

        // En producción (con STORAGE_PATH definido), construir URL manual
        $url = config('filesystems.disks.public.url') . '/' . $path;
        Log::info('[StorageHelper] getPublicUrl PROD: ' . $url . ' para path: ' . $path);
        return $url;
    }

    /**
     * Almacena un archivo en el disco público
     */
    public static function storePublicFile($file, $directory = '')
    {
        if (!$file) {
            return null;
        }

        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $directory ? $directory . '/' . $filename : $filename;

        // Usar el disco público configurado
        Storage::disk('public')->put($path, file_get_contents($file));

        return $path;
    }

    /**
     * Almacena un archivo con nombre personalizado
     */
    public static function storeFileAs($file, $directory, $filename)
    {
        if (!$file) {
            return null;
        }

        $path = $directory . '/' . $filename;
        Storage::disk('public')->put($path, file_get_contents($file));

        return $path;
    }

    /**
     * Elimina un archivo del storage público
     */
    public static function deletePublicFile($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return true;
        }
        return false;
    }

    /**
     * Verifica si un archivo existe en el storage público
     */
    public static function fileExists($path)
    {
        if (empty($path)) {
            Log::info('[StorageHelper] fileExists: path vacío');
            return false;
        }

        // Verificar usando Laravel Storage
        $laravelExists = Storage::disk('public')->exists($path);
        Log::info('[StorageHelper] fileExists: Laravel Storage para ' . $path . ': ' . ($laravelExists ? 'existe' : 'no existe'));
        
        // En desarrollo, también verificar físicamente el archivo
        if (config('app.env') === 'local') {
            $fullPath = storage_path('app/public/' . $path);
            $physicalExists = file_exists($fullPath);
            Log::info('[StorageHelper] fileExists: file_exists para ' . $fullPath . ': ' . ($physicalExists ? 'existe' : 'no existe'));
            // Si hay discrepancia, log para debug
            if ($laravelExists !== $physicalExists) {
                Log::warning("Storage discrepancy for file: {$path}. Laravel: " . ($laravelExists ? 'exists' : 'missing') . ", Physical: " . ($physicalExists ? 'exists' : 'missing'));
            }
            return $physicalExists;
        }
        
        return $laravelExists;
    }

    /**
     * Crea un directorio en el storage público
     */
    public static function makeDirectory($directory)
    {
        Storage::disk('public')->makeDirectory($directory);
    }

    /**
     * Obtiene la ruta completa del directorio storage público
     */
    public static function getStoragePath()
    {
        return Storage::disk('public')->path('');
    }
}