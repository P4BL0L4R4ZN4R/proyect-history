<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_route_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('route_id');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->date('assignment_date');
            $table->string('shift', 30)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('driver_route_assignments');
    }
};