<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- CSS y JS con Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles (si los usas) -->
    @livewireStyles
</head>
<body class="font-sans antialiased">
    {{-- Si tenías este componente, lo dejamos para mostrar banners de Jetstream --}}
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        {{-- Aquí cargamos tu menú (Livewire) --}}
        @livewire('navigation-menu')

        <!-- Encabezado de página opcional -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {!! $header !!}
                </div>
            </header>
        @endif


        <!-- Contenido principal -->
        <main class="py-4">
            @yield('content')
        </main>
    </div>


    {{-- Modales de Livewire --}}
    @stack('modals')

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- Lugar para scripts adicionales --}}
    @yield('scripts')
</body>
</html>
