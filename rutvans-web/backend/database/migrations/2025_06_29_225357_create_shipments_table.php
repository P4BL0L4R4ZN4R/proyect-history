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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->nullable()->constrained('sites')->onDelete('cascade');
            $table->string('folio', 50);
            $table->unsignedBigInteger('route_unit_id');
            $table->unsignedBigInteger('service_id');
            $table->string('sender_name', 100);
            $table->string('receiver_name', 100);
            $table->text('package_description')->nullable();
            $table->string('package_image', 255)->nullable();
            $table->string('status', 50)->default('Pendiente');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
            $table->foreign('route_unit_id')->references('id')->on('route_unit')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->index('site_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
