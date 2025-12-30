<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerDynamicMenu();
    }

    protected function registerDynamicMenu()
    {
        \Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            // Menú básico para todos los usuarios


            // Si el usuario está autenticado
            if (Auth::check()) {
                // Menú de Ventas (solo si tiene permisos)
                $this->addVentasMenu($event);

                // Menú de Administración (solo para admin)
                $this->addAdminMenu($event);
            }
        });
    }

    protected function addVentasMenu($event)
    {
        if (Auth::user()->can('acceder_ventas')) {
            $event->menu->add(['header' => 'VENTAS']);
            $event->menu->add([
                'text' => 'Realizar Venta',
                'url'  => '/ventas/create',
                'icon' => 'fas fa-fw fa-cart-plus',
                'can'  => 'crear_ventas'
            ]);
        }
    }

    protected function addAdminMenu($event)
    {

        if (!Auth::user()->hasAnyRole(['superadmin', 'admin'])) {
            return; // Sale si no tiene ninguno de los roles
        }

        // $event->menu->add(['header' => 'Inventario']);

        $event->menu->add([

            'text' => 'Clasificaciones',
            'url' => '/clasificacion',
            'icon' => 'fas fa-fw fa-solid fa-list',
        ]);

        $event->menu->add(['header' => 'Reportes']);

        $event->menu->add([

            'text' => 'Historial de ventas',
            'url' => '/ventas',
            'icon' => 'fas fa-fw fa-solid fa-receipt',

        ]);

        $event->menu->add([

            'text' => 'Historial de cortes',
            'url' => '/historial/cortes',
            'icon' => 'fa-solid fa-cash-register',



        ]);



        $event->menu->add(['header' => 'Administración']);

        $event->menu->add([
            'text'    => 'Usuarios',
            'icon'    => 'fas fa-fw fa-users',
            'submenu' => [
                [
                    'text' => 'Lista de Usuarios',
                    'url'  => '/users',
                    'icon' => 'fas fa-fw fa-list',
                    // 'can'  => 'editar_usuarios'
                ],
            ]
        ]);

         $event->menu->add([
            'text'    => 'Usuarios',
            'icon'    => 'fas fa-fw fa-users',

                'text' => 'Ajustes',
                'url' => '/configuracion/edit',
                'icon' => 'fas fa-fw fa-cog',

        ]);



    }





    public function register()
    {
        //
    }
}
