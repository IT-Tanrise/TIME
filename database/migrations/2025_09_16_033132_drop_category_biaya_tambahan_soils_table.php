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
        // Drop foreign key constraint first if it exists
        if (Schema::hasTable('description_biaya_tambahan_soils')) {
            Schema::table('description_biaya_tambahan_soils', function (Blueprint $table) {
                // Drop foreign key constraint if it exists
                $table->dropForeign(['category_id']);
                // Drop the category_id column
                $table->dropColumn('category_id');
            });
        }
        
        // Drop the category table
        Schema::dropIfExists('category_biaya_tambahan_soils');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the category table
        Schema::create('category_biaya_tambahan_soils', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->timestamps();
        });
        
        // Add category_id back to description table
        if (Schema::hasTable('description_biaya_tambahan_soils')) {
            Schema::table('description_biaya_tambahan_soils', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->after('id');
                $table->foreign('category_id')
                      ->references('id')
                      ->on('category_biaya_tambahan_soils')
                      ->onDelete('cascade');
            });
        }
    }
};