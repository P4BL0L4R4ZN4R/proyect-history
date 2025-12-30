<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncStorageLocal extends Command
{
    protected $signature = 'storage:sync-local';
    protected $description = 'Sincroniza archivos de storage/app/public a public/storage para desarrollo en Windows';

    public function handle()
    {
        $this->info('Sincronizando archivos de storage para desarrollo local...');
        
        $sourceDir = storage_path('app/public');
        $targetDir = public_path('storage');
        
        // Crear directorio destino si no existe
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
            $this->info('Directorio public/storage creado');
        }
        
        // Sincronizar archivos
        $this->syncDirectory($sourceDir, $targetDir);
        
        $this->info('Sincronización completada');
        return Command::SUCCESS;
    }
    
    private function syncDirectory($source, $target)
    {
        $files = File::allFiles($source);
        $synced = 0;
        
        foreach ($files as $file) {
            $relativePath = str_replace($source . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $targetPath = $target . DIRECTORY_SEPARATOR . $relativePath;
            $targetDir = dirname($targetPath);
            
            // Crear directorio si no existe
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }
            
            // Copiar archivo si no existe o es más nuevo
            if (!File::exists($targetPath) || File::lastModified($file->getPathname()) > File::lastModified($targetPath)) {
                File::copy($file->getPathname(), $targetPath);
                $synced++;
                $this->line("Sincronizado: {$relativePath}");
            }
        }
        
        $this->info("Total archivos sincronizados: {$synced}");
    }
}
