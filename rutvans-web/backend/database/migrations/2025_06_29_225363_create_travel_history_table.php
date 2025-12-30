<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('travel_history', function (Blueprint $table) {
            $table->id(); // id (PK)

            $table->unsignedBigInteger('sale_id'); // índice
            $table->unsignedBigInteger('route_unit_schedule_id'); // índice

            $table->enum('status', ['completed', 'canceled', 'delayed', 'in_progress'])->default('in_progress');

            $table->dateTime('actual_departure')->nullable();
            $table->dateTime('actual_arrival')->nullable();
            $table->tinyInteger('passenger_rating')->nullable();
            $table->string('report', 255);

            $table->timestamps(); // created_at, updated_at

            // Índices
            $table->index('sale_id');
            $table->index('route_unit_schedule_id');

            // Si tienes relaciones, puedes activar estas foreign keys:
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('route_unit_schedule_id')->references('id')->on('route_unit_schedule')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_history');
    }
};
