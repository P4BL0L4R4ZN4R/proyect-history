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
        // Crear el trigger sp_UpdateUserProfile para auditoría y validación
        DB::unprepared('
            DROP TRIGGER IF EXISTS sp_UpdateUserProfile;
            
            CREATE TRIGGER sp_UpdateUserProfile
            BEFORE UPDATE ON users
            FOR EACH ROW
            BEGIN
                -- Validaciones de datos críticos
                
                -- 1. Validar que el email tenga formato correcto
                IF NEW.email IS NOT NULL AND NEW.email != "" AND NEW.email NOT REGEXP "^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+[.][A-Za-z]+$" THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Email format is invalid";
                END IF;
                
                -- 2. Validar que el teléfono tenga formato correcto (solo números, espacios, guiones y paréntesis)
                IF NEW.phone_number IS NOT NULL AND NEW.phone_number != "" AND NEW.phone_number NOT REGEXP "^[0-9 ()+-]+$" THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Phone number format is invalid";
                END IF;
                
                -- 3. Validar que el nombre no esté vacío
                IF NEW.name IS NULL OR TRIM(NEW.name) = "" THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Name cannot be empty";
                END IF;
                
                -- 4. Validar longitud mínima del nombre (al menos 2 caracteres)
                IF LENGTH(TRIM(NEW.name)) < 2 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Name must be at least 2 characters long";
                END IF;
                
                -- 5. Prevenir duplicados de email (excepto el registro actual)
                IF EXISTS (
                    SELECT 1 FROM users 
                    WHERE email = NEW.email 
                    AND id != NEW.id 
                    AND email IS NOT NULL 
                    AND email != ""
                ) THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Email already exists";
                END IF;
                
                -- 6. Auditoría automática: actualizar updated_at
                SET NEW.updated_at = CURRENT_TIMESTAMP;
                
                -- 7. Preservar created_at original (no debe cambiar en UPDATE)
                SET NEW.created_at = OLD.created_at;
                
                -- 8. Validar que la dirección no sea excesivamente larga
                IF NEW.address IS NOT NULL AND LENGTH(NEW.address) > 500 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Address is too long (max 500 characters)";
                END IF;
                
                -- 9. Normalizar el email a minúsculas
                IF NEW.email IS NOT NULL THEN
                    SET NEW.email = LOWER(TRIM(NEW.email));
                END IF;
                
                -- 10. Normalizar el nombre (capitalizar primera letra)
                IF NEW.name IS NOT NULL THEN
                    SET NEW.name = TRIM(NEW.name);
                END IF;
                
            END
        ');
        
        // Crear también trigger para INSERT
        DB::unprepared('
            DROP TRIGGER IF EXISTS sp_InsertUserProfile;
            
            CREATE TRIGGER sp_InsertUserProfile
            BEFORE INSERT ON users
            FOR EACH ROW
            BEGIN
                -- Validaciones de datos críticos (similares al UPDATE)
                
                -- 1. Validar que el email tenga formato correcto
                IF NEW.email IS NOT NULL AND NEW.email != "" AND NEW.email NOT REGEXP "^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+[.][A-Za-z]+$" THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Email format is invalid";
                END IF;
                
                -- 2. Validar que el teléfono tenga formato correcto
                IF NEW.phone_number IS NOT NULL AND NEW.phone_number != "" AND NEW.phone_number NOT REGEXP "^[0-9 ()+-]+$" THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Phone number format is invalid";
                END IF;
                
                -- 3. Validar que el nombre no esté vacío
                IF NEW.name IS NULL OR TRIM(NEW.name) = "" THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Name cannot be empty";
                END IF;
                
                -- 4. Validar longitud mínima del nombre
                IF LENGTH(TRIM(NEW.name)) < 2 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Name must be at least 2 characters long";
                END IF;
                
                -- 5. Auditoría automática: establecer timestamps
                IF NEW.created_at IS NULL THEN
                    SET NEW.created_at = CURRENT_TIMESTAMP;
                END IF;
                
                IF NEW.updated_at IS NULL THEN
                    SET NEW.updated_at = CURRENT_TIMESTAMP;
                END IF;
                
                -- 6. Validar longitud de dirección
                IF NEW.address IS NOT NULL AND LENGTH(NEW.address) > 500 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Address is too long (max 500 characters)";
                END IF;
                
                -- 7. Normalizar el email a minúsculas
                IF NEW.email IS NOT NULL THEN
                    SET NEW.email = LOWER(TRIM(NEW.email));
                END IF;
                
                -- 8. Normalizar el nombre
                IF NEW.name IS NOT NULL THEN
                    SET NEW.name = TRIM(NEW.name);
                END IF;
                
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar los triggers
        DB::unprepared('DROP TRIGGER IF EXISTS sp_UpdateUserProfile');
        DB::unprepared('DROP TRIGGER IF EXISTS sp_InsertUserProfile');
    }
};