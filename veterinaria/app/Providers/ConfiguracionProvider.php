<?php

namespace App\Providers;

use App\Models\Variable;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class ConfiguracionProvider extends ServiceProvider
{
    protected $defaults = [
        'imagen' => 'images/logo-default.jpg',
        'empresa' => 'Locatel',
        'sucursal' => 'Sucursal Principal'
    ];

    public function register()
    {
        $this->app->singleton('configuracion', function () {
            return Cache::remember('config_global', now()->addHours(24), function () {
                return Variable::first() ?? (object) $this->defaults;
            });
        });

        $this->registerRoutes();
    }

    public function boot()
    {
        $this->configureAdminLTE();
        $this->shareWithViews();
    }

    protected function registerRoutes()
    {
        Route::prefix('admin')->middleware('web')->group(function () {
            Route::post('/configuracion/logo', function (Request $request) {
                $validated = $request->validate([
                    'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
                ]);

                $config = Variable::firstOrNew([]);

                // Eliminar imágenes anteriores
                if ($config->imagen) {
                    $this->deleteImageAndFavicon($config->imagen);
                }

                // Guardar nueva imagen
                $path = $request->file('logo')->store('public/images');
                $publicPath = str_replace('public/', 'storage/', $path);
                $config->imagen = $publicPath;
                $config->save();

                Cache::forget('config_global');

                return back()->with('success', 'Logo actualizado correctamente');
            })->name('configuracion.updateLogo');
        });
    }

    protected function deleteImageAndFavicon($imagePath)
    {
        if (Storage::exists($this->getStoragePath($imagePath))) {
            Storage::delete($this->getStoragePath($imagePath));
        }

        // También eliminar favicon relacionado
        $faviconName = pathinfo($imagePath, PATHINFO_FILENAME) . '.ico';
        $faviconPath = public_path('favicons/' . $faviconName);
        if (File::exists($faviconPath)) {
            File::delete($faviconPath);
        }
    }

    protected function configureAdminLTE()
    {
        $this->app->booted(function () {
            $config = app('configuracion');

            config([
                'adminlte.logo' => $config->empresa,
                'adminlte.logo_img' => $this->getImageUrl($config->imagen),
                'adminlte.logo_img_alt' => $config->empresa . ' Logo'
            ]);
        });
    }

    protected function shareWithViews()
    {
        view()->composer('*', function ($view) {
            $config = app('configuracion');

            $faviconPath = 'favicons/' . pathinfo($config->imagen, PATHINFO_FILENAME) . '.ico';

            $view->with([
                'configEmpresa' => $config->empresa,
                'configSucursal' => $config->sucursal,
                'configLogo' => $this->getImageUrl($config->imagen),
                'configFavicon' => asset($faviconPath) . '?v=' . time()
            ]);
        });
    }

    protected function getImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return asset($this->defaults['imagen']) . '?v=' . time();
        }

        return asset($imagePath) . '?v=' . time();
    }

    protected function getStoragePath($url)
    {
        return str_replace('storage/', 'public/', $url);
    }

    public static function updateConfig($data)
    {
        $config = app('configuracion');
        $config->fill($data)->save();
        Cache::forget('config_global');
    }
}
