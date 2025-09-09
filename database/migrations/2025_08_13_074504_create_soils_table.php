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
        Schema::create('soils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_id')->constrained('lands')->onDelete('cascade');
            $table->foreignId('business_unit_id')->constrained('business_units')->onDelete('cascade');
            $table->string('nama_penjual');
            $table->string('alamat_penjual');
            $table->string('nama_pembeli');
            $table->string('alamat_pembeli');
            $table->string('nomor_dan_tanggal_ppjb');
            $table->string('letak_tanah');
            $table->decimal('luas', 12, 2);
            $table->decimal('harga', 15, 2);
            $table->string('bukti_kepemilikan');
            $table->string('atas_nama');
            $table->text('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soils');
    }
};