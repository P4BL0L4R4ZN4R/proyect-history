<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_trackings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_route_assignment_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->json('location_log')->nullable();
            $table->string('status', 30)->default('en curso');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('driver_route_assignment_id')->references('id')->on('driver_route_assignments')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('trip_trackings');
    }
};