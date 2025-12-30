<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class StorageLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:link-custom {--force : Force the operation to run even if the link already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create storage link adapted for both local and production environments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (app()->environment('local')) {
            // En local, usar el comportamiento estándar de Laravel
            $this->handleLocalEnvironment();
        } else {
            // En producción, crear estructura personalizada para hosting
            $this->handleProductionEnvironment();
        }
    }

    private function handleLocalEnvironment()
    {
        $this->info('Setting up storage for LOCAL environment...');
        
        $publicPath = public_path('storage');
        $storagePath = storage_path('app/public');

        if (File::exists($publicPath)) {
            if ($this->option('force')) {
                if (is_link($publicPath)) {
                    unlink($publicPath);
                } else {
                    File::deleteDirectory($publicPath);
                }
                $this->info('Existing storage link/directory removed.');
            } else {
                $this->warn('Storage link/directory already exists. Use --force to recreate.');
                return;
            }
        }

        // Crear directorio storage/app/public si no existe
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
            $this->info('Created storage/app/public directory.');
        }

        // En Windows, crear directorio físico y configurar sincronización automática
        if (PHP_OS_FAMILY === 'Windows') {
            File::makeDirectory($publicPath, 0755, true);
            $this->info('Created public/storage directory for Windows.');
            
            // Copiar archivos existentes
            if (File::exists($storagePath)) {
                $this->copyStorageFiles($storagePath, $publicPath);
                $this->info('Copied existing files to public/storage.');
            }
        } else {
            // En Linux/Unix, crear enlace simbólico
            try {
                symlink($storagePath, $publicPath);
                $this->info('Storage symlink created successfully for local environment.');
            } catch (\Exception $e) {
                // Si falla el enlace simbólico, usar método de copia
                File::makeDirectory($publicPath, 0755, true);
                $this->copyStorageFiles($storagePath, $publicPath);
                $this->warn('Symlink failed, using directory copy method instead.');
            }
        }

        // Crear subdirectorios comunes
        $directories = [
            'coordinators',
            'drivers', 
            'units',
            'companies',
            'users',
            'temp'
        ];

        foreach ($directories as $dir) {
            $fullPath = $storagePath . '/' . $dir;
            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
                $this->info("Created directory in storage: {$dir}");
            }
        }

        $this->info('Local storage structure created successfully.');
    }

    private function handleProductionEnvironment()
    {
        $this->info('Setting up storage for PRODUCTION environment...');
        
        $storagePath = env('STORAGE_PATH', storage_path('app/public'));
        
        // Crear directorio de storage en la ruta de producción si no existe
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
            $this->info("Created production storage directory: {$storagePath}");
        }

        // Crear subdirectorios comunes
        $directories = [
            'coordinators',
            'drivers', 
            'units',
            'companies',
            'users',
            'temp'
        ];

        foreach ($directories as $dir) {
            $fullPath = $storagePath . '/' . $dir;
            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
                $this->info("Created directory: {$dir}");
            }
        }

        $this->info('Production storage structure created successfully.');
        $this->info("Storage path: {$storagePath}");
        $this->warn('Remember to create the symbolic link manually in public_html:');
        $this->warn('ln -s /home/u350475089/domains/rutvans.com/public_html/storage /home/u350475089/domains/rutvans.com/public_html/storage');
    }

    private function copyStorageFiles($source, $destination)
    {
        if (!File::exists($source)) {
            return;
        }

        $files = File::allFiles($source);
        
        foreach ($files as $file) {
            $relativePath = str_replace($source, '', $file->getPathname());
            $destPath = $destination . $relativePath;
            
            // Crear directorio de destino si no existe
            $destDir = dirname($destPath);
            if (!File::exists($destDir)) {
                File::makeDirectory($destDir, 0755, true);
            }
            
            // Copiar el archivo
            File::copy($file->getPathname(), $destPath);
        }
    }
}
