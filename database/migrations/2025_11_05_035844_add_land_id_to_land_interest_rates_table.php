<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('land_interest_rates', function (Blueprint $table) {
            $table->foreignId('land_id')->constrained('lands')->cascadeOnDelete()->after('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('land_interest_rates');
    }
};