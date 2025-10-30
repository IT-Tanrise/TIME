<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soils', function (Blueprint $table) {
            $table->string('status', 50)->default('active')->after('keterangan');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('soils', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
};