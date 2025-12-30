<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cashier_id');
            $table->unsignedBigInteger('site_id'); // Vinculado al sitio donde trabaja
            $table->string('shift_name', 50); // "Matutino", "Vespertino", "Nocturno", "Personalizado"
            $table->time('start_time'); // 08:00:00
            $table->time('end_time');   // 16:00:00
            $table->json('days_of_week'); // [1,2,3,4,5] (1=Lunes, 7=Domingo)
            $table->date('effective_from'); // Desde cuándo aplica este horario
            $table->date('effective_until')->nullable(); // Hasta cuándo (null = indefinido)
            $table->boolean('is_active')->default(true);
            $table->string('status', 30)->default('activo'); // activo, inactivo, suspendido
            $table->text('notes')->nullable(); // Notas del horario
            $table->unsignedBigInteger('created_by'); // Usuario que creó el horario
            $table->timestamps();
            
            $table->foreign('cashier_id')->references('id')->on('cashiers')->onDelete('cascade');
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Índices para consultas rápidas
            $table->index(['cashier_id', 'site_id', 'is_active']);
            $table->index(['effective_from', 'effective_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};