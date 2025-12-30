<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear el rol de super-admin si no existe
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web'
        ]);

        // Crear el usuario super-admin único para Rutvans
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@rutvans.com'],
            [
                'name' => 'Rutvans Super Admin',
                'password' => Hash::make('Rutvans2025!'),
                'email_verified_at' => now(),
            ]
        );

        // Asignar el rol de super-admin
        if (!$superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole('super-admin');
        }

        $this->command->info('✅ Super Admin creado exitosamente:');
        $this->command->info('📧 Email: superadmin@rutvans.com');
        $this->command->info('🔑 Password: Rutvans2025!');
        $this->command->info('👑 Rol: super-admin');
    }
}
