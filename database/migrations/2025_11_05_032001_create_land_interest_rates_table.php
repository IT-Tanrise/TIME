<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('land_interest_rates', function (Blueprint $table) {
            $table->id();
            $table->integer('month'); // 1-12
            $table->integer('year'); // e.g., 2024
            $table->decimal('rate', 5, 2); // e.g., 7.50 for 7.5%
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Ensure one rate per month-year combination
            $table->unique(['month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('land_interest_rates');
    }
};