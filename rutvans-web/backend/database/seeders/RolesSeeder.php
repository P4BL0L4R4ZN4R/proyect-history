<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles básicos del sistema con guard 'web'
        $roles = [
            'super-admin' => 'Super Administrador - Control total del sistema',
            'admin' => 'Administrador - Gestión general',
            'coordinate' => 'Coordinador - Gestión operativa',
            'driver' => 'Conductor - Operaciones de transporte',
            'cashier' => 'Cajero - Gestión de ventas',
            'client' => 'Cliente - Usuario final'
        ];

        foreach ($roles as $roleName => $description) {
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['name' => $roleName, 'guard_name' => 'web']
            );
            
            echo "Rol creado/verificado: {$roleName} (guard: web)\n";
        }

        echo "✅ Roles creados exitosamente!\n";
    }
}
