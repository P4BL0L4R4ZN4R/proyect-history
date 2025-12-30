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
        // Crear vista para calcular ingresos por servicio
        DB::unprepared('
            DROP VIEW IF EXISTS vw_ingresos_por_servicio;
            
            CREATE VIEW vw_ingresos_por_servicio AS
            SELECT 
                -- Ingresos por método de pago
                p.id as payment_method_id,
                p.name as payment_method_name,
                COUNT(s.id) as total_ventas,
                SUM(s.amount) as total_ingresos,
                AVG(s.amount) as ingreso_promedio,
                DATE(s.created_at) as fecha_venta
            FROM sales s
            INNER JOIN payments p ON s.payment_id = p.id
            WHERE s.status != "Cancelado"
            GROUP BY p.id, p.name, DATE(s.created_at)
            
            UNION ALL
            
            SELECT 
                -- Ingresos por sitio (como servicio)
                st.id as service_id,
                CONCAT("Sitio: ", st.name) as service_name,
                COUNT(s.id) as total_ventas,
                SUM(s.amount) as total_ingresos,
                AVG(s.amount) as ingreso_promedio,
                DATE(s.created_at) as fecha_venta
            FROM sales s
            INNER JOIN sites st ON s.site_id = st.id
            WHERE s.status != "Cancelado"
            GROUP BY st.id, st.name, DATE(s.created_at)
            
            ORDER BY fecha_venta DESC, total_ingresos DESC;
        ');
        
        // Crear función para obtener ingresos totales por período
        DB::unprepared('
            DROP FUNCTION IF EXISTS fn_calcular_ingresos_periodo;
            
            CREATE FUNCTION fn_calcular_ingresos_periodo(fecha_inicio DATE, fecha_fin DATE)
            RETURNS DECIMAL(10,2)
            READS SQL DATA
            DETERMINISTIC
            BEGIN
                DECLARE total_ingresos DECIMAL(10,2) DEFAULT 0.00;
                
                SELECT COALESCE(SUM(amount), 0.00) INTO total_ingresos
                FROM sales
                WHERE DATE(created_at) BETWEEN fecha_inicio AND fecha_fin
                  AND status != "Cancelado";
                
                RETURN total_ingresos;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar vista y función
        DB::unprepared('DROP VIEW IF EXISTS vw_ingresos_por_servicio');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_calcular_ingresos_periodo');
    }
};