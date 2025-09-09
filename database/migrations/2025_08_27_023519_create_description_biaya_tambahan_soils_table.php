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
        Schema::create('description_biaya_tambahan_soils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('category_biaya_tambahan_soils')->onDelete('cascade');
            $table->string('description');
            $table->timestamps();

            $table->index(['category_id', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('description_biaya_tambahan_soils');
    }
};