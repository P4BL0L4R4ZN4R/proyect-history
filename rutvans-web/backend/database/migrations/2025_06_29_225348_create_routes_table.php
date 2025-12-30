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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->nullable()->constrained('sites')->onDelete('cascade');
            $table->unsignedBigInteger('location_s_id');
            $table->unsignedBigInteger('location_f_id');
            $table->string('direction', 20)->default('ida'); // ida, vuelta, ambos
            $table->boolean('is_bidirectional')->default(false);
            $table->string('name', 100)->nullable(); // nombre amigable de la ruta
            $table->json('stops')->nullable(); // paradas intermedias
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('location_s_id')->references('id')->on('localities')->onDelete('cascade');
            $table->foreign('location_f_id')->references('id')->on('localities')->onDelete('cascade');
            $table->index('site_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
