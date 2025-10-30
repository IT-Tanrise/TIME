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
            $table->string('change_type', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to ENUM
        DB::statement("ALTER TABLE soil_approvals MODIFY COLUMN change_type ENUM('details', 'costs', 'delete', 'create') NOT NULL");
    }
};