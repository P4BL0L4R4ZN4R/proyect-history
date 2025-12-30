<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inter_site_communications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_site_id');
            $table->unsignedBigInteger('to_site_id');
            $table->string('type', 50);
            $table->text('content');
            $table->string('status', 30)->default('pendiente');
            $table->timestamps();
            $table->foreign('from_site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->foreign('to_site_id')->references('id')->on('sites')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('inter_site_communications');
    }
};