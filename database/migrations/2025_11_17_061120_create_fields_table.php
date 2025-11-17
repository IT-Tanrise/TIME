<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bidang', 255);
            $table->foreignId('business_unit_id')
                  ->constrained('business_units')
                  ->onDelete('restrict');
            $table->string('nomor_bidang', 20)->unique()->comment('Format: XXXX/YYY/NNN');
            $table->enum('status', ['active', 'inactive','pending'])->default('inactive');
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes untuk performa
            $table->index('business_unit_id');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};