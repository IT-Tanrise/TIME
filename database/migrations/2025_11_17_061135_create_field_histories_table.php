<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')
                  ->constrained('fields')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->enum('action', ['created', 'updated', 'deleted', 'restored']);
            $table->json('old_values')->nullable()->comment('Data sebelum perubahan');
            $table->json('new_values')->nullable()->comment('Data setelah perubahan');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            // Indexes untuk performa
            $table->index(['field_id', 'created_at']);
            $table->index('user_id');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_histories');
    }
};