<?php
// database/migrations/xxxx_xx_xx_create_soil_histories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('soil_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('soil_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action'); // 'created', 'updated', 'deleted'
            $table->json('changes')->nullable(); // Store what was changed
            $table->json('old_values')->nullable(); // Store old values
            $table->json('new_values')->nullable(); // Store new values
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->foreign('soil_id')->references('id')->on('soils')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['soil_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('soil_histories');
    }
};