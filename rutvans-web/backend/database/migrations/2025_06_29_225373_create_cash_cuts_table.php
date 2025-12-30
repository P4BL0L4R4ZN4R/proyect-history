<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_cuts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cashier_id');
            $table->unsignedBigInteger('relieved_by_cashier_id')->nullable(); // Cajero que lo releva
            $table->decimal('opening_amount', 10, 2); // Monto inicial
            $table->decimal('closing_amount', 10, 2); // Monto final
            $table->decimal('total_sales', 10, 2); // Total vendido en el turno
            $table->dateTime('shift_start'); // Hora de entrada
            $table->dateTime('shift_end'); // Hora de salida
            $table->dateTime('cut_time');
            $table->string('status', 30)->default('pendiente'); // pendiente, completado, revisado
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('cashier_id')->references('id')->on('cashiers')->onDelete('cascade');
            $table->foreign('relieved_by_cashier_id')->references('id')->on('cashiers')->onDelete('set null');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('cash_cuts');
    }
};