<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_storage_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->string('type', 50);
            $table->string('storage_path', 255);
            $table->string('drive_key', 255);
            $table->string('drive_folder', 255);
            $table->timestamps();
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('driver_storage_keys');
    }
};