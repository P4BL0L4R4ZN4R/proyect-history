<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'AdminLTE 3',
    'title_prefix' => 'Rut-Vans | ',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Rut</b>Vans',
    'logo_img' => 'asset/Logo_Van_RutVans.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'RutVans',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'asset/Logo_Van_RutVans.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'asset/Logo_Van_RutVans.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 150,
            'height' => 150,
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => true,
    'usermenu_desc' => true,
    'usermenu_profile_url' => 'true',


    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-secondary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-dark navbar-gray-dark',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        [
            'type' => 'navbar-search',
            'text' => 'search',
            'topnav_right' => true,
        ],
        
        [
            'type' => 'navbar-notification',
            'id' => 'notificaciones',
            'icon' => 'fas fa-bell',
            'icon_color' => 'warning',
            'label' => 0,
            'label_color' => 'warning',
            'url' => '#',
            'topnav_right' => true,
            'dropdown_mode' => true,
            'dropdown_flabel' => 'Cerrar',

            'update_cfg' => [
                'url' => 'api/notifications-data-navbar',
                'period' => 5,
            ],
        ],
        
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'search',
        ],
        [
            'text' => 'blog',
            'url' => 'admin/blog',
            'can' => 'manage-blog',
        ],
        [
            'header' => 'Panel administrador RutVans',
            'can' => 'super-admin', // Solo super-admin puede ver este header
        ],
        [
            'text' => 'Gestión de Clientes',
            'icon' => 'fas fa-industry',
            'can' => 'super-admin', // Solo super-admin
            'submenu' => [
                [
                    'text' => 'Empresas/Sindicatos',
                    'icon' => 'fas fa-building',
                    'url' => 'companies',
                ],
            ]
        ],
        [
            'header' => 'Panel administrativo',
            'can' => 'super-admin', // Solo super-admin puede ver este header
        ],
        [
            'text' => 'Roles y permisos',
            'icon' => 'fas fa-user-shield',
            'can' => 'super-admin', // Solo super-admin
            'submenu' => [
                [
                    'text' => 'Roles',
                    'icon' => 'fas fa-user-tag',
                    'url' => 'roles',
                ],
                [
                    'text' => 'Permisos',
                    'icon' => 'fas fa-key',
                    'url' => 'permissions',
                ],
                [
                    'text' => 'Asignar Permisos',
                    'icon' => 'fas fa-user-shield', // Ícono más representativo
                    'url' => 'roles-permissions',
                ],
            ]
        ],
        [
            'text' => 'Administrador',
            'icon' => 'fas fa-user-cog',
            'can' => 'admin-coordinate', // Usa el Gate definido
            'submenu' => [
                [
                    'text' => 'Sitios/Rutas',
                    'icon' => 'fas fa-route',
                    'url' => 'clients',
                    'can' => 'admin', // Solo admin puede ver sitios
                ],
                [
                    'text' => 'Conductores',
                    'url' => 'drivers',
                    'icon' => 'fas fa-taxi' // Representa la conducción de vehículos
                ],
                [
                    'text' => 'Cajeros',
                    'url' => 'cashiers',
                    'icon' => 'fas fa-cash-register' // Representa la gestión de pagos y dinero
                ],
            ],
        ],
        [
            'text' => 'Localidades',
            'icon' => 'fas fa-map-marker-alt', // Ícono más representativo para localidades
            'can' => 'super-admin', // Solo super-admin puede gestionar localidades
            'submenu' => [
                [
                    'text' => 'Gestionar localidades',
                    'url' => 'localidades',
                    'icon' => 'fas fa-edit'
                ],
                [
                    'text' => 'Exportar localidades',
                    'icon' => 'fas fa-file-export',
                    'url' => 'localidades-exp',
                ],
            ]
        ],
        [
            'header' => 'Calendario',
            'can' => 'admin-coordinate', // Solo admin y coordinate pueden ver este header
        ],
        [
            'text' => 'Calendario',
            'url' => 'route-unit-schedule',
            'icon' => 'fas fa-calendar-alt',
            'can' => 'admin-coordinate', // Usa el Gate definido
        ],

        [
            'header' => 'Gestión de Boletos',
            'can' => 'admin-coordinate', // Solo admin y coordinate pueden ver este header
        ],
        [
            'text' => 'Boletos',
            'icon' => 'fas fa-ticket-alt',
            'can' => 'admin-coordinate', // Solo admin y coordinate pueden gestionar boletos
            'submenu' => [
                [
                    'text' => 'Destinos Intermedios',
                    'url' => '/destino-intermedio',
                    'icon' => 'fas fa-map-marked-alt',  // Ícono más preciso para mapas
                    'classes' => isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'destino-intermedio') !== false ? 'bg-gradient-orange text-white' : '',
                ],
            ],
        ],

        [
            'header' => 'Gestión de Ventas',
            'can' => 'admin-coordinate', // Solo admin y coordinate pueden ver este header
        ],
        [
            'text' => 'Ventas',
            'icon' => 'fas fa-dollar-sign',  // Cambié a un ícono relacionado con ventas
            'can' => 'admin-coordinate', // Solo admin y coordinate pueden ver ventas
            'submenu' => [
                [
                    'text' => 'Historial de Ventas',
                    'url' => 'sales/history', // <-- Este es el cambio clave
                    'icon' => 'fas fa-cash-register',
                    'classes' => isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'sales/history') !== false ? 'bg-gradient-orange text-white' : '',
                ],
            ],
        ],

        [
            'header' => 'Gestión de Rutas y Unidades',
            'can' => 'admin-coordinate', // Solo admin y coordinate pueden ver este header
        ],
        [
            'text' => 'Rutas y Unidades',
            'icon' => 'fas fa-directions',  // Ícono más relacionado con rutas
            'can' => 'admin-coordinate', // Solo admin y coordinate pueden gestionar rutas
            'submenu' => [
                [
                    'text' => 'Rutas',
                    'icon' => 'fas fa-route',
                    'url' => 'rutas',
                ],
                [
                    'text' => 'Rutas - Unidades',
                    'url' => '/rutas-unidades',
                    'icon' => 'fas fa-bus-alt',
                    'classes' => isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'rutas-unidades') !== false ? 'bg-gradient-orange text-white' : '',
                ],
                [
                    'text' => 'Administrar Unidades',
                    'url' => '/units',
                    'icon' => 'fas fa-shuttle-van',  // Vehículo más general
                    'classes' => isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'unidades') !== false ? 'bg-gradient-orange text-white' : '',
                ],
    [
                    'text' => 'Tipos de Tarifas',
                    'url' => 'tarifas',
                    'icon' => 'fas fa-tag',
                ],

            ],
        ],
         [
            'text' => 'Otros',
            'icon' => 'fas fa-cogs',
            'can' => 'admin-coordinate', // Solo admin y coordinate pueden ver otros módulos
            'submenu' => [
                // [
//     'text' => 'Administrar Envíos',
//     'url' => '/envios',
//     'icon' => 'fas fa-truck',
// ],
                 [
                    'text' => 'Administrar Envíos',
                    'url' => '/shippings',
                    'icon' => 'fas fa-truck',
                ],
                [
                    'text' => 'Administrar Conductores',
                    'url' => '/conductores',
                    'icon' => 'fas fa-id-card-alt',
                ],
                [
                    'text' => 'Métodos de Pago',
                    'route'  => 'metodoPago.index',
                    'icon' => 'fas fa-money-check-alt',
                ],
            ],
        ],
        ['header' => 'Usuario'],
        [
            'text' => 'profile',
            'url' => '/perfil',
            'icon' => 'fas fa-fw fa-user',
            'classes' => isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/perfil') !== false ? 'bg-white text-dark' : '',
        ],
        [
            'text' => 'change_password',
            'url' => 'admin/settings',
            'icon' => 'fas fa-fw fa-lock',
        ],
        [
            'header' => 'Otros Módulos',
            'can' => 'admin-coordinate', // Solo admin y coordinate pueden ver este header
        ],
       

    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => true,
];
