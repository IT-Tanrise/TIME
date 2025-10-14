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
        Schema::create('biaya_tambahan_interest_soils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soil_id')->constrained('soils')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('remarks');
            $table->bigInteger('harga_perolehan')->default(0);
            $table->decimal('bunga', 5, 2)->default(0); // e.g., 7.50 for 7.5%
            $table->timestamps();
            
            // Add index for better query performance
            $table->index('soil_id');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_tambahan_interest_soils');
    }
};