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
        Schema::table('soils', function (Blueprint $table) {
            $table->date('shgb_expired_date')->nullable()->after('bukti_kepemilikan_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soils', function (Blueprint $table) {
            $table->dropColumn('shgb_expired_date');
        });
    }
};