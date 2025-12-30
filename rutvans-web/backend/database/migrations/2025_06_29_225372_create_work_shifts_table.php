<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('unit_id');
            $table->date('date');
            $table->string('shift', 30);
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->string('status', 30)->default('activo');
            $table->timestamps();
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('work_shifts');
    }
};