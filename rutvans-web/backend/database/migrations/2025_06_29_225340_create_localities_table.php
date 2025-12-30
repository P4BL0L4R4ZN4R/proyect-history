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
        Schema::create('localities', function (Blueprint $table) {
            $table->id(); // Esto crea un BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->decimal('longitude', 10, 6);
            $table->decimal('latitude', 10, 6);
            $table->string('locality', 100);
            $table->string('street', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('municipality', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->default('México');
            $table->string('locality_type', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('localities');
    }
};
