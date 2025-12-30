<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard según el rol del usuario
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Redirigir según el rol del usuario
        if ($user->hasRole('super-admin')) {
            return $this->superAdminDashboard();
        } elseif ($user->hasRole('admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('coordinate')) {
            return $this->coordinateDashboard();
        } elseif ($user->hasRole('driver')) {
            return $this->driverDashboard();
        } elseif ($user->hasRole('cashier')) {
            return $this->cashierDashboard();
        } elseif ($user->hasRole('client')) {
            return $this->clientDashboard();
        } else {
            // Usuario sin rol definido
            return $this->defaultDashboard();
        }
    }

    /**
     * Dashboard para Super Admin
     */
    private function superAdminDashboard()
    {
        // Por ahora redirigir al perfil, más tarde se puede crear una vista específica
        return redirect()->route('profile.edit')->with('info', 'Como Super Admin, puedes gestionar roles y permisos desde el menú lateral.');
    }

    /**
     * Dashboard para Admin
     */
    private function adminDashboard()
    {
        // Usar el dashboard actual del admin (HomeController)
        $homeController = new \App\Http\Controllers\HomeController();
        $request = request(); // Obtener la instancia actual de Request
        return $homeController->index($request);
    }

    /**
     * Dashboard para Driver
     */
    private function driverDashboard()
    {
        return view('dashboards.driver')->with([
            'welcomeMessage' => '¡Bienvenido Conductor! Consulta tus rutas y horarios asignados.',
            'userRole' => 'Conductor'
        ]);
    }

    /**
     * Dashboard para Cashier
     */
    private function cashierDashboard()
    {
        return view('dashboards.cashier')->with([
            'welcomeMessage' => '¡Bienvenido Cajero! Gestiona ventas y transacciones.',
            'userRole' => 'Cajero'
        ]);
    }

    /**
     * Dashboard para Client
     */
    private function clientDashboard()
    {
        return view('dashboards.client')->with([
            'welcomeMessage' => '¡Bienvenido Cliente! Consulta tus envíos y servicios.',
            'userRole' => 'Cliente'
        ]);
    }

    /**
     * Dashboard por defecto para usuarios sin rol
     */
    private function defaultDashboard()
    {
        return redirect()->route('profile.edit')->with('warning', 'Tu cuenta no tiene un rol asignado. Contacta con el administrador.');
    }

    /**
     * Obtener la ruta del dashboard según el rol
     */
    public static function getDashboardRoute($user)
    {
        if (!$user) {
            return route('login');
        }

        if ($user->hasRole('super-admin')) {
            return route('profile.edit');
        } elseif ($user->hasRole('admin')) {
            return route('dashboard');
        } elseif ($user->hasRole('coordinate')) {
            return route('coordinator.dashboard');
        } elseif ($user->hasRole('driver')) {
            return route('dashboard.role');
        } elseif ($user->hasRole('cashier')) {
            return route('dashboard.role');
        } elseif ($user->hasRole('client')) {
            return route('dashboard.role');
        } else {
            return route('profile.edit');
        }
    }
}
