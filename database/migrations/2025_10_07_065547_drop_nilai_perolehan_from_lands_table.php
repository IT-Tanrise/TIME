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
        Schema::table('lands', function (Blueprint $table) {
            $table->dropColumn('nilai_perolehan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lands', function (Blueprint $table) {
            $table->decimal('nilai_perolehan', 15, 2)->after('tahun_perolehan');
        });
    }
};