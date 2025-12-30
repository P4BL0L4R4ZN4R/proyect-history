<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear el procedimiento almacenado sp_GetUsersByRole
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetUsersByRole');
        
        DB::unprepared('
            CREATE PROCEDURE sp_GetUsersByRole(IN role_name VARCHAR(255))
            BEGIN
                DECLARE role_exists INT DEFAULT 0;
                
                -- Verificar si el rol existe
                SELECT COUNT(*) INTO role_exists
                FROM roles 
                WHERE roles.name COLLATE utf8mb4_unicode_ci = role_name COLLATE utf8mb4_unicode_ci 
                  AND roles.guard_name COLLATE utf8mb4_unicode_ci = "web" COLLATE utf8mb4_unicode_ci;
                
                -- Si el rol no existe, lanzar error
                IF role_exists = 0 THEN
                    SIGNAL SQLSTATE "45000" 
                    SET MESSAGE_TEXT = "El rol especificado no fue encontrado en el sistema";
                END IF;
                
                -- Si el rol existe, devolver los usuarios asociados
                SELECT 
                    u.id as user_id,
                    u.name as user_name,
                    u.email as user_email,
                    u.phone_number,
                    u.address,
                    u.created_at as usuario_creado,
                    r.name as rol_asignado
                FROM users u
                INNER JOIN model_has_roles mhr ON u.id = mhr.model_id
                INNER JOIN roles r ON mhr.role_id = r.id
                WHERE r.name COLLATE utf8mb4_unicode_ci = role_name COLLATE utf8mb4_unicode_ci
                  AND r.guard_name COLLATE utf8mb4_unicode_ci = "web" COLLATE utf8mb4_unicode_ci
                  AND mhr.model_type COLLATE utf8mb4_unicode_ci = "App\\\\Models\\\\User" COLLATE utf8mb4_unicode_ci
                ORDER BY u.name ASC;
                
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar el procedimiento almacenado
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetUsersByRole');
    }
};