<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Helpers\StorageHelper;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Directiva personalizada para mostrar imágenes del storage
        Blade::directive('storageImg', function ($expression) {
            return "<?php echo App\Helpers\StorageHelper::getPublicUrl($expression); ?>";
        });

        // Directiva para verificar si existe un archivo en storage
        Blade::directive('storageExists', function ($expression) {
            return "<?php echo App\Helpers\StorageHelper::fileExists($expression) ? 'true' : 'false'; ?>";
        });
    }
}
