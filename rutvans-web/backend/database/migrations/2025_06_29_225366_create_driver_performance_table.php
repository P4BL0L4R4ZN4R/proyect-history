<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('driver_performance', function (Blueprint $table) {
            $table->id();
            $table->integer('driver_id')->nullable();
            $table->dateTime('last_updated')->nullable();
            $table->integer('total_trips_completed')->nullable();
            $table->double('driver_rating')->nullable();
            $table->tinyInteger('punctuality_percentage')->nullable();
            $table->integer('orders_delivered')->nullable();
            $table->integer('orders_cancelled')->nullable();
            $table->integer('average_trip_time_minutes')->nullable();
            $table->double('average_trip_distance_km')->nullable();
            $table->integer('active_hours_this_month')->nullable();
            $table->integer('recommendations_received')->nullable();
            $table->integer('reports_received')->nullable();
            $table->json('weekly_trips_completed')->nullable();
            
            $table->index('driver_id', 'driver_id_fk');
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_performance');
    }
};