<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->string('type', 50); // tipo de documento: licencia, INE, etc.
            $table->string('photo_path', 255);
            $table->date('expiration_date')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('driver_documents');
    }
};