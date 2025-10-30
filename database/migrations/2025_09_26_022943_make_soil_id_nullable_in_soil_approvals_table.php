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
        Schema::table('soil_approvals', function (Blueprint $table) {
            // Make soil_id nullable to support create operations
            $table->unsignedBigInteger('soil_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soil_approvals', function (Blueprint $table) {
            // Revert back to NOT NULL if needed
            $table->unsignedBigInteger('soil_id')->nullable(false)->change();
        });
    }
};