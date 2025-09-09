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
        Schema::table('biaya_tambahan_soils', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['nominal', 'keterangan']);
            
            // Add new columns
            $table->foreignId('category_id')->constrained('category_biaya_tambahan_soils')->onDelete('cascade');
            $table->foreignId('description_id')->constrained('description_biaya_tambahan_soils')->onDelete('cascade');
            $table->enum('cost_type', ['standard', 'non_standard'])->default('standard');
            
            // Add indexes for better performance
            $table->index(['soil_id', 'category_id']);
            $table->index(['soil_id', 'cost_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_tambahan_soils', function (Blueprint $table) {
            // Drop new columns
            $table->dropForeign(['category_id']);
            $table->dropForeign(['description_id']);
            $table->dropColumn(['category_id', 'description_id', 'cost_type']);
            
            // Restore old columns
            $table->string('nominal');
            $table->string('keterangan')->nullable();
        });
    }
};