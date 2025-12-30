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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del sindicato/empresa
            $table->string('business_name')->nullable(); // Razón social
            $table->string('rfc')->nullable(); // RFC de la empresa
            $table->unsignedBigInteger('locality_id')->nullable(); // Localidad principal
            $table->text('address'); // Dirección principal
            $table->string('phone', 20); // Teléfono principal
            $table->string('email')->nullable(); // Email de contacto
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();
            
            // Índices
            $table->index('locality_id');
            $table->index('status');
            
            // Relación con localidades
            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
