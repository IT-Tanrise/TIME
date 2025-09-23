<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('soil_approvals', function (Blueprint $table) {
            // Change the change_type from string to enum with delete support
            $table->enum('change_type', ['details', 'costs', 'delete'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soil_approvals', function (Blueprint $table) {
            // Revert back to string type
            $table->string('change_type')->change();
        });
    }
};