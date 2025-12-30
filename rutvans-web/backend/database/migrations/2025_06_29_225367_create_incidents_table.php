<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('details')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('category', 50)->nullable();
            $table->text('image')->nullable();
            $table->string('urgency', 50)->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->timestamp('created_at')->nullable();
            
            $table->index('user_id', 'incident_user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('incidents');
    }
};