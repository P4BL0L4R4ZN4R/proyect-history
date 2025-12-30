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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('plate', 50);
            $table->string('brand', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->integer('capacity');
            $table->string('photo', 255)->nullable();
            $table->string('status', 30)->default('activo'); // activo, inactivo, mantenimiento
            $table->unsignedBigInteger('site_id');
            $table->timestamps();
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
