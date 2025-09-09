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
            $table->string('nominal', 255)->nullable()->after('soil_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_tambahan_soils', function (Blueprint $table) {
            $table->dropColumn('nominal');
        });
    }
};