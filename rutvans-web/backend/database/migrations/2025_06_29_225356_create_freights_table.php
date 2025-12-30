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
      Schema::create('freights', function (Blueprint $table) {
        $table->id();
        $table->foreignId('site_id')->nullable()->constrained('sites')->onDelete('cascade');
        $table->string('folio', 50);
        $table->unsignedBigInteger('service_id');
        $table->unsignedBigInteger('driver_id');
        $table->date('start_date');
        $table->date('end_date');
        $table->time('start_time');
        $table->time('end_time');
        $table->string('name', 100);
        $table->string('origin', 100);
        $table->string('destination', 100);
        $table->integer('number_people'); // ← CORREGIDO
        $table->string('status', 50)->default('Pendiente');
        $table->decimal('amount', 10, 2);

        $table->timestamps();
        $table->foreign('service_id')
            ->references('id')->on('services')
            ->onDelete('cascade');
        $table->foreign('driver_id')
            ->references('id')->on('drivers')
            ->onDelete('cascade');
        $table->index('site_id');
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freights');
    }
};
