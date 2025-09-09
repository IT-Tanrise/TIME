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
            $table->string('nop_pbb', 255)->nullable()->after('atas_nama');
            $table->string('nama_notaris_ppat', 255)->nullable()->after('nop_pbb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soils', function (Blueprint $table) {
            $table->dropColumn(['nop_pbb', 'nama_notaris_ppat']);
        });
    }
};