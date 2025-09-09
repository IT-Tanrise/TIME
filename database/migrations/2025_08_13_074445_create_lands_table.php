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
        Schema::create('lands', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi_lahan');
            $table->integer('tahun_perolehan');
            $table->decimal('nilai_perolehan', 15, 2);
            $table->text('alamat')->nullable();
            $table->string('link_google_maps')->nullable();
            $table->string('kota_kabupaten')->nullable();
            $table->string('status');
            $table->text('keterangan')->nullable();
            $table->decimal('nominal_b', 15, 2)->nullable();
            $table->decimal('njop', 15, 2)->nullable();
            $table->decimal('est_harga_pasar', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lands');
    }
};