<nav class="bg-white border-b border-gray-100" x-data="{ open: false, userMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Izquierda: Logo + Navegación -->
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="/logo.png" alt="Logo" class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Links Desktop -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a href="{{ route('dashboard') }}"
                       class="{{ request()->routeIs('dashboard') ? 'text-indigo-700 border-b-2 border-indigo-700' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 rounded-md text-sm font-medium">
                        Dashboard
                    </a>
                    
                    @hasrole('super-admin')
                    <a href="{{ route('clients.index') }}"
                       class="{{ request()->routeIs('clients.*') ? 'text-indigo-700 border-b-2 border-indigo-700' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-building mr-1"></i>
                        Clientes
                    </a>
                    @endhasrole
                    
                    <a href="{{ route('localidades.index') }}"
                       class="{{ request()->routeIs('localidades.*') ? 'text-indigo-700 border-b-2 border-indigo-700' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 rounded-md text-sm font-medium">
                        Localidades
                    </a>
                </div>
            </div>

            <!-- Derecha: Usuario -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                <div class="relative" @click.away="userMenuOpen = false">
                    <button @click="userMenuOpen = !userMenuOpen" type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ Auth::user()->name }}
                        <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="userMenuOpen" x-transition
                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                         style="display: none;">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="text-sm text-gray-700 underline hover:text-gray-900">Iniciar sesión</a>
                @endauth
            </div>

            <!-- Botón menú móvil -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open" type="button"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Abrir menú principal</span>
                    <svg :class="{ 'hidden': open, 'block': !open }" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg :class="{ 'block': open, 'hidden': !open }" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menú móvil -->
    <div x-show="open" x-transition class="sm:hidden" id="mobile-menu" style="display: none;">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Dashboard
            </a>
            
            @hasrole('super-admin')
            <a href="{{ route('clients.index') }}"
               class="{{ request()->routeIs('clients.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                <i class="fas fa-building mr-1"></i>
                Clientes
            </a>
            @endhasrole
            
            <a href="{{ route('localidades.index') }}"
               class="{{ request()->routeIs('localidades.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Localidades
            </a>
        </div>

        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cerrar sesión</button>
                </form>
            </div>
        </div>
        @else
        <div class="pt-4 pb-1 border-t border-gray-200 px-4">
            <a href="{{ route('login') }}" class="text-sm text-gray-700 underline hover:text-gray-900">Iniciar sesión</a>
        </div>
        @endauth
    </div>
</nav>

<!-- Alpine.js para el menú desplegable -->
<script src="//unpkg.com/alpinejs" defer></script>
